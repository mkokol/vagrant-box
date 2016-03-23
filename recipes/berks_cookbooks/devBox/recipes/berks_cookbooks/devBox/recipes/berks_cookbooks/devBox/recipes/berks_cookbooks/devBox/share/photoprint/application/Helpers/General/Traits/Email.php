<?php

trait Helpers_General_Traits_Email
{
    protected $emailTesting = false;

    /**
     * Send to user an email confirmation when he has registered
     */
    protected function sendUserRegistrationConfirmation($userName, $email, $password, $usersHash)
    {
        $this->loadTranslation(['mail/general', 'mail/user/registration'], null, 'mail');
        $t = Helpers_General_ControllerAction::getLoadedTranslation();

        $emailHelper = new Helpers_General_Mail();
        $emailHelper->addTo($email, $userName);
        $emailHelper->setSubject($t->_('your_account_has_been_created_photoprint'));
        $emailHelper->setBodyHtmlTemplate(
            'user/registration',
            ['email' => $email, 'password' => $password, 'hash' => $usersHash]
        );
        $this->send($emailHelper);
    }

    /**
     * Send to user an email with a link for reset password
     */
    protected function sendUserResetPasswordLink($userName, $email, $hash)
    {
        $this->loadTranslation(['mail/general', 'mail/user/reset-password'], null, 'mail');
        $t = Helpers_General_ControllerAction::getLoadedTranslation();

        $emailHelper = new Helpers_General_Mail();
        $emailHelper->addTo($email, $userName);
        $emailHelper->setSubject($t->_('password_recovery_photoprint'));
        $emailHelper->setBodyHtmlTemplate(
            'user/reset-password',
            ['userName' => $userName, 'email' => $email, 'hash' => $hash]
        );
        $this->send($emailHelper);
    }

    /**
     * send to user email with information about new order
     */
    protected function sendUserOrderConfirmation($userName, $email, $orderId)
    {
        $this->loadTranslation(['products', 'mail/general', 'mail/user/order-confirmation'], null, 'mail');
        $t = Helpers_General_ControllerAction::getLoadedTranslation();

        $order = new Orders();
        $orderInfo = $order->fetchRow('id = ' . $orderId)->toArray();
        $baskets = new Baskets();
        $orderItems = $baskets->getOrdersItems($orderId);

        $emailHelper = new Helpers_General_Mail();
        $emailHelper->addTo($email, $userName);
        $emailHelper->setSubject($t->_('your_order_confirmation_on_photoprint'));
        $emailHelper->setBodyHtmlTemplate(
            'user/order-confirmation',
            ['orderInfo' => $orderInfo, 'orderItems' => $orderItems]
        );
        $this->send($emailHelper);
    }

    protected function sendUserOrderReviewRequest($language, $userName, $email, $hash)
    {
        $this->loadTranslation(['products', 'mail/general', 'mail/user/order-review-request'], $language, 'mail');
        $t = Helpers_General_ControllerAction::getLoadedTranslation();

        $emailHelper = new Helpers_General_Mail();
        $emailHelper->addTo($email, $userName);
        $emailHelper->setSubject($t->_('leave_a_review_on_your_order'));
        $emailHelper->setBodyHtmlTemplate(
            'user/order-review-request',
            ['hash' => $hash]
        );
        $this->send($emailHelper);
    }

    /*******************************************************************
     * notification for administrators
     ******************************************************************/

    protected function sendAdminOrderConfirmation($userName, $email, $orderId)
    {
        $this->loadTranslation(['products', 'mail/general', 'mail/admin/order-confirmation'], null, 'mail');
        $t = Helpers_General_ControllerAction::getLoadedTranslation();

        $order = new Orders();
        $orderInfo = $order->fetchRow('id = ' . $orderId)->toArray();
        $baskets = new Baskets();
        $orderItems = $baskets->getOrdersItems($orderId);

        $emailHelper = new Helpers_General_Mail();
        $emailHelper->addTo($email, $userName);
        $emailHelper->setSubject($t->_('new_order_on_photoprint'));
        $emailHelper->setBodyHtmlTemplate(
            'admin/order-confirmation',
            ['orderInfo' => $orderInfo, 'orderItems' => $orderItems]
        );
        $this->send($emailHelper);
    }

    protected function sendAdminNewPartnerRegistration($userName, $email)
    {
        $this->loadTranslation(['products', 'mail/general'], null, 'mail');
        $t = Helpers_General_ControllerAction::getLoadedTranslation();

        $emailHelper = new Helpers_General_Mail();
        $emailHelper->addTo($email, $userName);
        $emailHelper->setSubject($t->_('partner_registration_notification'));
        $emailHelper->setBodyHtmlTemplate(
            'admin/partner-registration',
            []
        );
        $this->send($emailHelper);
    }

    protected function sendAdminPartnerCreatedProduct($userName, $email)
    {
        $this->loadTranslation(['products', 'mail/general'], null, 'mail');
        $t = Helpers_General_ControllerAction::getLoadedTranslation();

        $emailHelper = new Helpers_General_Mail();
        $emailHelper->addTo($email, $userName);
        $emailHelper->setSubject($t->_('partner_create_a_new_product_notification'));
        $emailHelper->setBodyHtmlTemplate(
            'admin/partner-created-product',
            []
        );
        $this->send($emailHelper);
    }

    private function send(Helpers_General_Mail $emailHelper)
    {
        if ($this->emailTesting) {
            echo $emailHelper->getBody();
        } else {
            $emailHelper->send();
        }
    }
}
