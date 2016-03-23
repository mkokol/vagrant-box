<?php

class UserController extends Helpers_General_ControllerAction
{

    /**
     * registartion users in system
     */
    public function registrationAction()
    {
        $this->loadTranslation('user/registration');

        $captcha = '';
        if ($this->_request->isPost()) {
            $formData = $this->_request->getPost();
            if (isset($formData['captcha']['input'])) {
                $captcha = $formData['captcha']['input'];
            }
        }

        $form = new Helpers_Forms_RegisterForm($captcha);
        if ($this->_request->isPost()) {
            $formData = $this->_request->getPost();
            if ($form->isValid($formData)) {
                $current_date = date('Y-m-d H:i:s');
                $users = new Users();
                $userId = $users->insert([
                    'user_name' => $formData['user_name'],
                    'email'     => strtolower($formData['email']),
                    'phone'     => $formData['phone'],
                    'password'  => md5($formData['password']),
                    'created'   => $current_date
                ]);
                Users::login(
                    strtolower($formData['email']),
                    $formData['password']
                );

                $usersHashes = new UsersHashes();
                $usersHash = md5(uniqid($userId . $formData['email']));
                $now = date('Y-m-d H:i:s');
                $usersHashes->insert([
                    'user_id'    => $userId,
                    'hash'       => $usersHash,
                    'type'       => 'confirmation',
                    'status'     => 'new',
                    'updated_on' => $now,
                    'created_on' => $now
                ]);
                $this->sendUserRegistrationConfirmation(
                    $formData['user_name'],
                    $formData['email'],
                    $formData['password'],
                    $usersHash
                );
                $this->redirectTo('user/registered');
            } else {
                $this->view->form = $form;
            }
        } else {
            $this->view->form = $form;
        }
    }

    /**
     * show congratulation after registration
     */
    public function registeredAction()
    {
        $this->loadTranslation('user/registered');
    }

    /**
     * show window when user forgot a password.
     */
    public function resendPasswordAction()
    {
        $this->loadTranslation('user/resend_password');
        $resendPasswordForm = new Helpers_Forms_ResendPassword();

        if ($this->_request->isPost()) {
            $formData = $this->_request->getPost();

            if ($resendPasswordForm->isValid($formData)) {
                $users = new Users();

                if ($user = $users->getInfoByEmail($formData["email"])) {
                    $hash = md5(microtime() + $formData["email"]);
                    $usersHashes = new UsersHashes();
                    $now = date('Y-m-d H:i:s');
                    $usersHashes->insert([
                        'user_id'    => $user['id'],
                        'hash'       => $hash,
                        'type'       => 'password_reset',
                        'status'     => 'new',
                        'updated_on' => $now,
                        'created_on' => $now
                    ]);
                    $this->sendUserResetPasswordLink($user['user_name'], $formData["email"], $hash);
                    $this->redirectTo('user/resent-password');
                }
            }
        }

        $this->view->resendPasswordForm = $resendPasswordForm;
    }

    /**
     * congratulation after sending email for reset password.
     */
    public function resentPasswordAction()
    {
        $this->loadTranslation('user/resent_password');
    }

    /**
     * Show set new password form for reset
     */
    public function resetPasswordAction()
    {
        $this->loadTranslation('user/reset_password');

        $hash = $this->_request->getQuery('hash');
        $usersHashes = new UsersHashes();
        $userId = $usersHashes->getUserId($hash, 'password_reset');
        $allowReset = false;

        if ($userId) {
            $allowReset = true;
            $resetPasswordForm = new Helpers_Forms_ResetPassword();

            if ($this->_request->isPost()) {
                $formData = $this->_request->getPost();

                if ($resetPasswordForm->isValid($formData)) {
                    $users = new Users();
                    $users->update(
                        ['password' => md5($formData['password'])],
                        'id = ' . $userId
                    );
                    $usersHashes->update(
                        ['status' => 'used'],
                        "hash = '$hash'"
                    );
                    $this->view->passwodr_is_resetet = true;
                }
            }
            $this->view->reset_pass_form = $resetPasswordForm;
        }

        $this->view->allowReset = $allowReset;
    }

    /**
     * Show popup for changing password on edit page
     */
    public function changepasswndAction()
    {
        $this->loadTranslation('user/changepasswnd');
        $this->view->resetPassForm = new Helpers_Forms_ChangePassword();
    }

    /**
     * Changing password on edit page
     */
    public function changepassAction()
    {
        $resetPassForm = new Helpers_Forms_ChangePassword();
        $this->removeDefaultView();
        $response = ['status' => 'success'];
        if ($this->_request->isPost()) {
            $formData = $this->_request->getPost();
            if (!$resetPassForm->validateAndSave($formData)) {
                $errors = $resetPassForm->getErrors();
                $errorMessages = $resetPassForm->getMessages();
                foreach ($errors as $field => $error) {
                    if (isset($error[0])) {
                        $errors[$field] = $errorMessages[$field][$error[0]];
                    }
                }
                $response = [
                    'status' => 'error',
                    'errors' => $errors
                ];
            }
        }
        $this->viewJson($response);
    }

    public function confirmationAction()
    {
        $this->loadTranslation('user/confirmation');
        $hash = $this->_request->getQuery('hash');
        $usersHashes = new UsersHashes();
        $userId = $usersHashes->getUserId($hash, 'confirmation');

        if ($userId) {
            $user = new Users();
            $user->update(
                ['email_is_confirmed' => 1],
                'id = ' . $userId
            );
            $usersHashes->update(
                ['status' => 'used'],
                "hash = '$hash'"
            );
            $this->view->emailIsConfirmed = true;
        }
    }

    /**
     * Build login popup window
     */
    public function loginWndAction()
    {
        if (!$this->_request->isXmlHttpRequest()) {
            $this->error404();
        }

        $this->appendFile('login');
        $this->loadTranslation('user/login_wnd');
    }

    /**
     * login user in to system
     */
    public function loginAction()
    {
        $this->removeDefaultView();
        $result = $this->login();
        $this->viewJson($result);
    }

    /**
     * logout user from system
     */
    function logoutAction()
    {
        Session::getInstance()->setData(['auth' => null]);
        Zend_Auth::getInstance()->clearIdentity();
        $this->redirectTo('');
    }

    /**
     * Edit user information page
     */
    public function infoAction()
    {
        $this->view->topMenuItems = Users::getUserMenuItems();
        $this->loadTranslation(['user_menu', 'user/info']);
        $this->appendStylesheet('pages/user_info');
        $this->appendFile(['jquery.imgareaselect.min', 'fileuploader', 'user/info']);
        $updateUserForm = new Helpers_Forms_UpdateUserInfo();
        if (Zend_Auth::getInstance()->getIdentity()->avatar_id) {
            $images = new Images();
            $this->view->avatar = $images->fetchRow('id = ' . Zend_Auth::getInstance()->getIdentity()->avatar_id);
        }
        if ($this->_request->isPost()) {
            $formData = $this->_request->getPost();
            $updateUserForm->validateAndSave($formData);
        }
        $this->view->updateUserForm = $updateUserForm;
    }

    /**
     * popup for upload avatar
     */
    public function uploadavatarwndAction()
    {
        $this->loadTranslation('user/uploadavatarwnd');
    }

    /**
     * Upload temporary avatar photo
     */
    function uploadphotoAction()
    {
        $this->removeDefaultView();
        // list of valid extensions, ex. array("jpeg", "xml", "bmp")
        $allowedExtensions = ["jpeg", "jpg", 'png', 'gif'];
        // max file size in bytes
        $uploader = new ImagesUploader_qqFileUploader($allowedExtensions);
        $result = $uploader->handleUpload();
        $result['imgPath'] = $this->_request->getBaseUrl() . '/' . $result['imgPath'];
        $result['folder'] = $result['folder'];
        $this->viewJson($result);
    }

    /**
     * upload and crop avatar image
     */
    function saveavatarAction()
    {
        $this->removeDefaultView();
        $MAX_MBOX_WIDTH = 400;
        $MAX_MBOX_HEIGHT = 260;
        $images = new Images();
        $image = $images->readImage(
            Images::IMG_AVATAR_TMP_DIR . $this->_request->getPost('folder') . '/' . $this->_request->getPost('imgName')
        );
        $width = $images->getImageWidth($image);
        $height = $images->getImageHeight($image);
        $delta = (($width / $MAX_MBOX_WIDTH) > ($height / $MAX_MBOX_HEIGHT)) ? $width / $MAX_MBOX_WIDTH : $height / $MAX_MBOX_HEIGHT;

        $x1 = round($this->_request->getPost('x1') * $delta);
        $y1 = round($this->_request->getPost('y1') * $delta);
        $w = round($this->_request->getPost('w') * $delta);
        $h = round($this->_request->getPost('h') * $delta);

        $newImageAvatarLarge = $images->resize($image, 0, 0, $x1, $y1, $w, $h, $w, $h);
        $croppedImgName = $images->createImageName($newImageAvatarLarge);
        $images->saveImage($newImageAvatarLarge, $croppedImgName, 'o', Images::IMG_AVATAR_DIR);

        $newImageAvatarSmall = $images->resize($newImageAvatarLarge, 0, 0, 0, 0, 200, 276, $w, $h);
        $imgForResponse = $images->saveImage($newImageAvatarSmall, $croppedImgName, 'a', Images::IMG_AVATAR_DIR);

        $s_x1 = round($this->_request->getParam('s_x1') * (200 / 159));
        $s_y1 = round($this->_request->getParam('s_y1') * (200 / 159));
        $s_w = round($this->_request->getParam('s_w') * (200 / 159));
        $s_h = round($this->_request->getParam('s_h') * (200 / 159));

        $newImageAvatarIcon = $images->resize($newImageAvatarSmall, 0, 0, $s_x1, $s_y1, 70, 70, $s_w, $s_h);
        $images->saveImage($newImageAvatarIcon, $croppedImgName, 'p', Images::IMG_AVATAR_DIR);
        $newImageAvatarIcon = $images->resize($newImageAvatarSmall, 0, 0, $s_x1, $s_y1, 50, 50, $s_w, $s_h);
        $images->saveImage($newImageAvatarIcon, $croppedImgName, 'i', Images::IMG_AVATAR_DIR);

        $imageId = $images->insertNewImage(
            [
                'user_id'   => Users::getCarrentUserId(),
                'name'      => $croppedImgName,
                'extension' => $images->extension
            ]
        );
        $users = new Users();
        $users->update(
            ['avatar_id' => $imageId],
            'id = ' . Users::getCarrentUserId()
        );
        Zend_Auth::getInstance()->getIdentity()->avatar_id = $imageId;
        $result['imgSrc'] = $this->_request->getBaseUrl() . '/' . $imgForResponse;
        $this->viewJson($result);
    }

    function deleteavatarwndAction()
    {
        $this->loadTranslation('user/deleteavatarwnd');
    }

    function deleteavatarAction()
    {
        $this->removeDefaultView();
        $users = new Users();
        $userId = Zend_Auth::getInstance()->getIdentity()->id;
        $users->update(['avatar_id' => null], "id = $userId");
        $result['imgSrc'] = $this->_request->getBaseUrl() . '/public/theme/avatar/origin.jpg';
        Zend_Auth::getInstance()->getIdentity()->avatar_id = null;
        $this->viewJson($result);
    }

    /**
     * view users gallery page
     */
    public function galleryAction()
    {
        $this->view->topMenuItems = Users::getUserMenuItems();
        $this->loadTranslation('user_menu');
        $this->appendFile('user/gallery');
    }

    /**
     * view user basket (login and not login user)
     */
    public function basketAction()
    {
        if (Zend_Auth::getInstance()->hasIdentity()) {
            $this->view->topMenuItems = Users::getUserMenuItems();
        }

        $this->loadTranslation(['user_menu', 'products', 'user/basket']);
        $this->appendFile('user/basket');
        $this->appendWidgets('tablesorter');

        $baskets = new Baskets();
        $userId = Users::getCarrentUserId();
        $basketItems = $baskets->getBasketItem($userId);

        foreach ($basketItems as $key => $product) {
            $xmlData = new SimpleXMLElement(stripslashes($product['dataXml']));
            $basketItems[$key]['previewGroup'] = (string)$xmlData->group;
            $basketItems[$key]['previewTemplate'] = '';

            if ($basketItems[$key]['previewGroup'] == 'cup') {
                $basketItems[$key]['previewTemplate'] = isset($xmlData->template) ? (string)$xmlData->template : '';
            } else if ($basketItems[$key]['previewGroup'] == 'mousepad') {
                $basketItems[$key]['previewTemplate'] = (string)$xmlData->item;
            }

            $basketItems[$key]['previewModel'] = (string)$xmlData->item;
        }

        $this->view->basketItems = $basketItems;
    }

    /**
     * fetching images by albom id
     */
    public function imagesAction()
    {
        $this->loadTranslation('user/images');
        $this->view->albumId = $albumId = $this->_request->getQuery('albumId', 0);
        $albums = new Albums();
        $this->view->albumName = $albums->getAlbumName($albumId);
        $images = new UsersImages();
        $this->view->allImages = $images->getAllAlbumImages($this->_request->getQuery('albumId', null));
    }

    public function uploadimageswndAction()
    {
        $this->loadTranslation('user/uploadimageswnd');
        $image_form = new Helpers_Forms_ImageUpload();
        $this->view->image_form = $image_form;
    }

    public function uploadimagesAction()
    {
        $this->removeDefaultView();
        $result = ['status' => 'error'];
        $imageForm = new Helpers_Forms_ImageUpload();

        if ($this->_request->isPost() && $this->_request->getPost('currentForm') == 'ImageUpload') {
            $formData = $this->_request->getPost();
            $rez = $imageForm->validateAndUploadImage($formData);

            if ($rez['status']) {
                $result = [
                    'status'    => 'success',
                    'imageName' => $rez['imageName'],
                    'extension' => $rez['extension']
                ];
            } else {
                $result['errors'] = $imageForm->getMessages();
            }
        }

        $this->viewJson($result);
    }

    public function deleteimageswndAction()
    {
        $this->loadTranslation('user/deleteimageswnd');
    }

    public function deleteimagesAction()
    {
        $this->removeDefaultView();
        $userId = Users::getCarrentUserId();
        $listId = explode(";", $this->_request->getParam('listId'));
        $usersImages = new UsersImages();
        foreach ($listId as $kay => $imgId) {
            $usersImages->delete("id  = '$imgId' AND user_id = '$userId'");
        }
        $this->viewJson(['status' => 'success']);
    }

    /**
     * rotate image
     * @return string
     */
    public function rotateimageAction()
    {
        $this->removeDefaultView();
        $fullName = $this->_request->getQuery('img_go');
        $degrees = $this->_request->getQuery('degree');
        $fullNameSplited = explode('.', $fullName);
        $last_degrees = (ctype_digit($fullNameSplited[1]) == 2) ? $fullNameSplited[1] : 0;
        $name = $fullNameSplited[0];
        $extension = $fullNameSplited[count($fullNameSplited) - 1];
        $newDegree = (!@class_exists('Imagick')) ? $last_degrees - $degrees : $last_degrees + $degrees;

        if (360 <= $newDegree) {
            $newDegree -= 360;
        } else if ($newDegree < 0) {
            $newDegree += 360;
        }

        $image = Images::getImagePath('.', $name, 'p', $extension);
        $rotatedImgPath = Images::getTmpImagePath($name, $newDegree, $extension);
        $result['status'] = 'success';

        if ($newDegree != 0) {
            if (file_exists($rotatedImgPath)) {
                $result['urlname'] = $this->_request->getBaseUrl() . '/' . $rotatedImgPath;
            } else {
                $images = new Images();
                $source = $images->readImage($image);
                $rotateImg = $images->rotateImage($source, $newDegree);
                $images->saveImage($rotateImg, $name, $newDegree, Images::IMG_TMP_DIR);
                $result['urlname'] = $this->_request->getBaseUrl() . '/' . $rotatedImgPath;
            }
        } else {
            $result['urlname'] = Images::getImagePath($this->_request->getBaseUrl(), $name, 'p', $extension);
        }

        $this->viewJson($result);
    }

    /**
     * get user alboms list
     */
    public function albumsAction()
    {
        $this->loadTranslation('user/albums');
        $userDate = Zend_Auth::getInstance()->getIdentity();
        $albums = new Albums();
        $this->view->allAlbums = $albums->fetchAll('user_id = ' . $userDate->id)->toArray();
        $images = new UsersImages();
        $this->view->hasNotSortedImages = $images->hasNotSortedImages($userDate->id);
    }

    public function createalbumwndAction()
    {
        $this->loadTranslation('user/createalbumwnd');
    }

    /**
     * create user albums
     */
    public function createalbumAction()
    {
        $this->removeDefaultView();
        $albums = new Albums();
        $albums->insert([
            'user_id' => Users::getCarrentUserId(),
            'title'   => $this->_request->getParam('newAlbumName')
        ]);
        $this->viewJson(['status' => 'success']);
    }

    /**
     * delete user albums wnd
     */
    public function deletealbumswndAction()
    {
        $this->loadTranslation('user/delete_albums_wnd');
    }

    /**
     * delete user albums
     */
    public function deletealbumsAction()
    {
        $this->removeDefaultView();
        $userId = Users::getCarrentUserId();
        $listId = explode(";", $this->_request->getParam('listId'));
        $albums = new Albums();
        $usersImages = new UsersImages();

        foreach ($listId as $albumId) {
            if ($albumId == 0) {
                $usersImages->delete("album_id is NULL AND user_id = '$userId'");
            } else {
                $usersImages->delete("album_id = '$albumId' AND user_id = '$userId'");
                $albums->delete("id = '$albumId' AND user_id = '$userId'");
            }
        }

        $this->viewJson(['status' => 'success']);
    }

    /**
     * copy user item to basket
     */
    public function addBasketItemAction()
    {
        $this->removeDefaultView();

        $id = $this->_request->getParam('product-id', '');
        $item = $this->_request->getParam('item', '');
        $color = $this->_request->getParam('color', '');
        $size = $this->_request->getParam('size', '');

        $productsItems = new ProductsItems();
        $productsItem = $productsItems->getUpdatedProductItemXml($id, $item, $color, $size);
        $basket = new Baskets();

        if ($basket->validateItem($productsItem['xmlData'])) {
            $basket->insert([
                'user_id'         => Users::getCarrentUserId(),
                'product_item_id' => $id,
                'product_group'   => $productsItem['xmlData']->group,
                'status'          => 'created',
                'dataXml'         => $productsItem['xmlData']->asXML(),
                'count'           => 1,
                'payment'         => ProductsItems::getProductItemPrice($productsItem, $item),
                'date'            => date('Y-m-d H:i:s')
            ]);
            $productsItems->update(
                ['rating' => $productsItem['rating'] + 1],
                'id = ' . $id
            );
            $result = ['status' => 'success'];
        } else {
            $result = ['status' => 'error'];
        }

        $this->viewJson($result);
    }

    /**
     * Show popup that propose user to redirect him to basket
     */
    public function gotobusketAction()
    {
        $this->loadTranslation('user/gotobusket');
    }

    /**
     * add new custom user item to basket
     */
    public function addtobusketAction()
    {
        $this->removeDefaultView();
        $xml = $this->_request->getQuery('xml');
        $group = $this->_request->getQuery('group');
        $id = $this->_request->getQuery('id');
        $basket = new Baskets();
        $data = [
            'user_id'       => Users::getCarrentUserId(),
            'product_group' => $group,
            'status'        => 'created',
            'dataXml'       => $xml,
            'count'         => '1'
        ];
        if ($id == 'none') {
            $data['date'] = date('Y-m-d H:i:s');
            $basket->insert($data);
        } else {
            $basket->update($data, 'id = ' . $id);
        }
        $this->viewJson(['status' => 'success']);
    }

    public function addtobusketwndAction()
    {
        $this->loadTranslation('user/addtobusketwnd');
    }

    /**
     * Popup for deleting item from basket
     */
    public function deleteBasketItemWndAction()
    {
        $this->loadTranslation('user/deletebusketitemwnd');
        $this->view->itemId = $this->_request->getParam('id');
    }

    /**
     * AJAX
     * Deleting item from basket
     */
    public function deleteBasketItemAction()
    {
        $this->removeDefaultView();
        $basket = new Baskets();
        $basket->update(
            ['status' => 'deleted'],
            'id = ' . $this->_request->getParam('id')
        );
        $this->viewJson(['status' => 'success']);
    }

    /**
     * AJAX
     * Change basket item count
     */
    public function changeItemCountAction()
    {
        $this->removeDefaultView();
        $basket = new Baskets();
        $basket->update(
            [
                'count' => $this->_request->getParam('itemCount')
            ],
            'id = ' . $this->_request->getParam('itemId')
        );
        $this->viewJson(['status' => 'success']);
    }

    /**
     * Create new order
     */
    public function createOrderAction()
    {
        $this->appendStylesheet('pages/user_create_order');
        $this->loadTranslation('user/create_order');

        $selectedItems = $this->_request->getQuery('items', '');
        $orderForm = (Zend_Auth::getInstance()->hasIdentity())
            ? new Helpers_Forms_OrderForLoggedUser($selectedItems)
            : new Helpers_Forms_OrderForNotLoggedUser($selectedItems);

        if ($this->_request->isPost()) {
            $formData = $this->_request->getPost();

            if ($orderForm->isValid($formData)) {
                $orderId = $orderForm->save($selectedItems, $this->refKey);

                $userDate = Zend_Auth::getInstance()->getIdentity();
                $this->sendAdminOrderConfirmation('Коколюс Володимир', 'vovychk@gmail.com', $orderId);
                $this->sendAdminOrderConfirmation('Коколюс Михайло', 'mickokolius@gmail.com', $orderId);
                $this->sendUserOrderConfirmation($userDate->user_name, $userDate->email, $orderId);

                $this->redirectTo("user/thank-you/order/$orderId");
            }
        }

        $this->view->orderForm = $orderForm;
    }

    public function orderReviewAction()
    {
        $this->loadTranslation('user/order_review');
        $hash = $this->_request->getQuery('hash');
        $usersHashes = new UsersHashes();
        $userHash = $usersHashes->fetchRow("hash = '$hash'");
        $orderId = null;

        if ($userHash) {
            $data = unserialize($userHash->data);
            $orderId = $data['orderId'];
        }

        if ($this->_request->isPost()) {
            $ordersReviews = new OrdersReviews();
            $ordersReviews->insert([
                'order_id'        => $orderId,
                'service_rating'  => $this->_request->getParam('service_rating', -1),
                'service_comment' => $this->_request->getParam('service_comment', ''),
                'product_rating'  => $this->_request->getParam('product_rating', -1),
                'product_comment' => $this->_request->getParam('product_comment', ''),
                'created_on'      => date('Y-m-d H:i:s')
            ]);
            $this->redirectTo("user/thanks-for-order-review");
        }
    }

    public function thanksForOrderReviewAction()
    {
        $this->loadTranslation('user/thanks_for_order_review');
    }

    /**
     *
     */
    public function loginOnCreateOrderAction()
    {
        $this->removeDefaultView();

        $userIdBeforeSignUp = Users::getCarrentUserId();

        $result = $this->login();

        if (Zend_Auth::getInstance()->hasIdentity()) {
            $baskets = new Baskets();
            $baskets->update(
                ['user_id' => Users::getCarrentUserId()],
                'user_id = \'' . $userIdBeforeSignUp . '\''
            );
        }

        $this->viewJson($result);
    }

    /**
     * Order thank you page
     */
    public function thankYouAction()
    {
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $this->redirectTo('error/error');
        }

        $this->loadTranslation(['user/thank_you', 'products']);

        $orderId = $this->_request->getParam('order', '');
        $userId = Zend_Auth::getInstance()->getIdentity()->id;

        $order = new Orders();
        $this->view->order = $order->fetchRow('id = ' . $orderId . ' AND user_id = ' . $userId);

        $baskets = new Baskets();
        $orderedItems = $baskets->getOrdersItems($orderId);

        foreach ($orderedItems as $key => $value) {
            $xmlData = new SimpleXMLElement(stripslashes($value['dataXml']));

            $orderedItems[$key]['name'] = (string)$xmlData->item;
            $orderedItems[$key]['price'] = ProductsItems::getProductItemPrice($value);
        }

        $this->view->orderedItems = $orderedItems;
    }
}
