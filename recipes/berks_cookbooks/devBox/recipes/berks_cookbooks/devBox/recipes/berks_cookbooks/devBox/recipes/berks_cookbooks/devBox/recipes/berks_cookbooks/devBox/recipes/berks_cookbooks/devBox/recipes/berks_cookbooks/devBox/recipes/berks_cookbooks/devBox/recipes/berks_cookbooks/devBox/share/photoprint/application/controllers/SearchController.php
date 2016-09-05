<?php

class SearchController extends Helpers_General_ControllerAction
{
    // нажаль в даний момент у нас немає нічого по даному запити проте наша компанія постійно
    // працює над розширенням каталогу продукції. Якщо ви залишити свій емейл та опис футболки
    // яка вас цікавить то ми повідомимо вас коли даний який відповідатиме вашому запиту буде створений
    //
    // * даний запит не являється замовленням, проте ви знатимете першими коли у нас зяветься
    // футболка яка підпадає під ваш опис.

    public function indexAction()
    {
        $this->loadTranslation('search/index');
        $q = $this->_request->getQuery('q', '');

        if (!Zend_Auth::getInstance()->hasIdentity() || Zend_Auth::getInstance()->getIdentity()->permission != 'admin'){
            $searchContentLog = new SearchContentLog();
            $searchContentLog->insert([
                'user_id'    => Users::getCarrentUserId(),
                'query'      => $q,
                'created_on' => date('Y-m-d H:i:s')
            ]);
        }

        $searchContent = new SearchContent();
        $this->view->productList = $searchContent->findCompatibles($q);
    }
}
