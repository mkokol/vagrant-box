<?php

class Helpers_Forms_ImageUpload extends Helpers_Forms_GenForm
{

    private $img_small = array('width' => 160, 'height' => 120, 'folder' => 'small');
    private $img_large = array('width' => 572, 'height' => 572, 'folder' => 'large');

    public function __construct()
    {
        parent::__construct('forms/imageupload');

        $this->setName('uploadImagesForm');
        $this->setAttrib('id', 'uploadImagesForm');
        $this->setMethod('post');
        $this->setAttrib('enctype', 'multipart/form-data');
        $this->setAction($this->request->getBaseUrl() . '/' . $this->language . '/user/uploadimages');

        $current_form = new Zend_Form_Element_Hidden('currentForm');
        $current_form->setValue('ImageUpload');
        $current_form->setLabel('ImageUpload');

        $theme = new Zend_Form_Element_Hidden('theme');
        $theme->setValue($this->request->getParam('theme'));
        $theme->setLabel('theme');

        $albumId = new Zend_Form_Element_Hidden('albumId');
        $albumId->setValue($this->request->getParam('albumId'));
        $albumId->setLabel('albumId');

        $createdBy = new Zend_Form_Element_Hidden('createdBy');
        $createdBy->setValue($this->request->getParam('createdBy'));
        $createdBy->setLabel('createdBy');

        $image = new Zend_Form_Element_File('img');
        $image->setLabel($this->t->_('upload_an_image') . ':')
            ->setRequired(true)
            ->addValidator(new Zend_Validate_File_Count(1))
            ->addValidator('Size', false, 24 * 1024 * 1024)
            ->addValidator('Extension', false, ['jpeg', 'jpg', 'png', 'gif'])
            ->setAttrib('style', 'display:none;');


        $title = $this->createElement('text', 'title');
        $title->setLabel($this->t->_('title_image') . ' :');

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel($this->t->_('add_image'));
        $submit->setAttrib('class', 'button');

        $this->addElements(array($current_form, $albumId, $theme, $createdBy, $image, $title, $submit));

        foreach ($this->getElements() as $element) {
            if ($element instanceof Zend_Form_Element_Hidden) {
                foreach ($element->getDecorators() as $decorator) {
                    $decorator->setOption('class', 'hidden');
                }
            }
        }
    }

    public function validateAndUploadImage($formData)
    {
        if ($this->isValid($formData)) {
            $file = $this->img->getFileInfo();
            $images = new Images();
            $image = $images->readImage($file['img']['name'], $file['img']['tmp_name']);

            if ($image) {
                $userId = Users::getCarrentUserId();
                $imgName = $images->createImageName($image);
                $imageId = $images->getExistImageId($imgName);
                if (!$imageId) {
                    $w_image = $images->getImageWidth($image);
                    $h_image = $images->getImageHeight($image);
                    $images->saveImage($image, $imgName, 'o', Images::IMG_ORIGIN_DIR);

                    $koefPreview = (($w_image / 572) > ($h_image / 572)) ? $w_image / 572 : $h_image / 572;
                    $w_s_image = round($w_image / $koefPreview);
                    $h_s_image = round($h_image / $koefPreview);
                    $newImagePreview = $images->resize($image, 0, 0, 0, 0, $w_s_image, $h_s_image, $w_image, $h_image);
                    $images->saveImage($newImagePreview, $imgName, 'p', Images::IMG_DIR);

                    $koefSmall = (($w_image / 160) > ($h_image / 120)) ? $w_image / 160 : $h_image / 120;
                    $w_s_image = round($w_image / $koefSmall);
                    $h_s_image = round($h_image / $koefSmall);
                    $newImageSmall = $images->resize($image, 0, 0, 0, 0, $w_s_image, $h_s_image, $w_image, $h_image);
                    $images->saveImage($newImageSmall, $imgName, 's', Images::IMG_DIR);

                    $imageId = $images->insertNewImage(
                        array(
                            'user_id'   => $userId,
                            'name'      => $imgName,
                            'extension' => $images->extension
                        )
                    );
                }
                $usersImages = new UsersImages();
                $uploadedImgName = pathinfo($file['img']['name']);
                $usersImages->insert(
                    array(
                        'user_id'  => $userId,
                        'image_id' => $imageId,
                        'album_id' => ($formData['albumId']) ? $formData['albumId'] : null,
                        'tema'     => ($formData['theme']) ? $formData['theme'] : $userId,
                        'created'  => date('Y-m-d H:i:s'),
                        'alt'      => ($formData['title']) ? $formData['title'] : $uploadedImgName['filename']
                    )
                );
                return array('status' => true, 'imageName' => $imgName, 'extension' => $images->extension);
            }
        }
        return array('status' => false, 'imageName' => null, 'extension' => null);
    }
}
