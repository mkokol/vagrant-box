<?php

class Helpers_General_Mail
{
    private $mail;
    private $fromMail = 'admin@photoprint.in.ua';
    private $fromName = 'photoprint.in.ua';

    private $emailParams = [];
    private $mailTemplateDir;

    public function __construct()
    {
        $this->mail = new Zend_Mail('utf-8');
        $this->mail->setFrom($this->fromMail, $this->fromName);
        $this->mailTemplateDir = APPLICATION_PATH
            . DIRECTORY_SEPARATOR . 'views'
            . DIRECTORY_SEPARATOR . 'mail'
            . DIRECTORY_SEPARATOR;
    }

    public function addTo($email, $name)
    {
        $this->emailParams['addTo'] = [
            'email' => $email,
            'name' => $name,
        ];
    }

    public function setSubject($subject)
    {
        $this->emailParams['subject'] = '=?utf-8?B?' . base64_encode($subject) . '?=';
    }

    public function setBodyHtml($body)
    {
        $this->emailParams['body'] = $body;
    }

    public function setBodyHtmlTemplate($template, $params = [])
    {
        $config = Zend_Registry::get('config');

        $view = new Zend_View(['encoding' => "utf-8"]);
        $view->setScriptPath($this->mailTemplateDir);
        $view->setHelperPath($config->viewHelper->path, $config->viewHelper->prefix);
        $view->t = Helpers_General_ControllerAction::getLoadedTranslation('mail');

        foreach ($params as $key => $value) {
            $view->{$key} = $value;
        }

        $this->emailParams['body'] = $view->render($template . '.phtml');
    }

    public function getBody()
    {
        return $this->emailParams['body'];
    }

    public function send()
    {
        if (!isset($this->emailParams['addTo'])) {
            throw new \Exception('Please set email address.');
        }
        $this->mail->addTo(
            $this->emailParams['addTo']['email'],
            $this->emailParams['addTo']['name']
        );

        if (!isset($this->emailParams['subject'])) {
            throw new \Exception('Please set email subject.');
        }
        $this->mail->setSubject($this->emailParams['subject']);

        if (!isset($this->emailParams['body'])) {
            throw new \Exception('Please set email body.');
        }
        $this->mail->setBodyHtml($this->emailParams['body']);

        if (Zend_Controller_Front::getInstance()->getBaseUrl() != '') {
            $emailLogFile = fopen(
                BASE_PATH . '/log/emails/mail-' . date("Y-M-d_H.i.s") . '~' . rand(1, 99999) . ".html",
                "w"
            ) or die("Unable to open file!");
            fwrite($emailLogFile, $this->emailParams['body']);
            fclose($emailLogFile);
        } else {
            $this->mail->send();
        }
    }
}
