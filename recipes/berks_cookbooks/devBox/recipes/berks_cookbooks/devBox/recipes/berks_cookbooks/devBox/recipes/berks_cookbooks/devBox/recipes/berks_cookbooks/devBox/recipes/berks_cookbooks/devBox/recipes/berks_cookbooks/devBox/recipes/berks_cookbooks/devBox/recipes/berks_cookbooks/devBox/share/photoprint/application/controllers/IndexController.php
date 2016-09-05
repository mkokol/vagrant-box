<?php

class IndexController extends Helpers_General_ControllerAction
{
    /**
     * redirect to ru language if language not selected
     */
    public function redirectAction()
    {
        $this->redirectTo('', ['code' => 301]);
    }

    /**
     * home page
     */
    public function indexAction()
    {
        $this->appendWidgets('featureList');
        $this->loadTranslation(['products', 'index/index']);

        $productsGroup = new ProductsGroup();
        $this->view->publicProductsGroup = $productsGroup->getPublic();
    }

    /**
     * popup messages
     */
    public function messageAction()
    {
        $this->loadTranslation('index/message');
        $this->view->message = $this->_request->getQuery('message', '');
    }

    /**
     * Site status for pingdom
     */
    public function pingdomAction()
    {
        $this->removeDefaultView();
        $this->getResponse()
            ->setHeader('content-type', 'text/plain', true)
            ->setBody(
                "<pingdom_http_custom_check>\n"
                . "<status>OK</status>\n"
                . "<response_time>" . round($this->getLoadTime(), 3) . "</response_time>\n"
                . "</pingdom_http_custom_check>\n"
            );
    }

    public function bluePrintAction (){
    }

    public function testEmailAction()
    {
        $mailType = $this->_request->getQuery('mailType', '');
        $this->emailTesting = true;

        if ($mailType == 'registration') {
            $this->sendUserRegistrationConfirmation('M K', 'mickokolius@gmail.com', 'password', '3176c45b33d240ee6fe6831bad3a33cb');
        }

        if ($mailType == 'resetPassword') {
            $this->sendUserResetPasswordLink('M K', 'mickokolius@gmail.com', '169a031837626667f69edcf39c3e1071');
        }

        if ($mailType == 'confirmation'){
            $orderId = $this->_request->getQuery('orderId', 485);
            $this->sendUserOrderConfirmation('M K', 'mickokolius@gmail.com', $orderId);
        }

        if ($mailType == 'confirmationAdmin'){
            $orderId = $this->_request->getQuery('orderId', 485);
            $this->sendAdminOrderConfirmation('M K', 'mickokolius@gmail.com', $orderId);
        }

        if ($mailType == 'partnerRegistrationAdmin'){
            $this->sendAdminNewPartnerRegistration('M K', 'mickokolius@gmail.com');
        }

        if ($mailType == 'partnerCreatedProductAdmin'){
            $this->sendAdminPartnerCreatedProduct('M K', 'mickokolius@gmail.com');
        }

        if ($mailType == 'orderReviewRequest'){
            $this->sendUserOrderReviewRequest('ru', 'M K', 'mickokolius@gmail.com', '8a62379bf5bf9df282cec6ebe6356bb2');
        }

        exit;
    }

    public function languageAction()
    {
        $turn = $this->_request->getQuery('turn', 'off');
        $userSession = new Zend_Session_Namespace('UserSession');
        $userSession->supportLanguage = ($turn == 'on') ? true : false;

        if ($userSession->previousUrl) {
            $this->redirect($userSession->previousUrl);
        }

        $this->redirectTo('');
    }
}
