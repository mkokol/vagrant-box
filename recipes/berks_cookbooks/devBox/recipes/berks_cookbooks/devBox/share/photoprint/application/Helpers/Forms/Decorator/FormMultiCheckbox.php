<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: FormMultiCheckbox.php 20096 2010-01-06 02:05:09Z bkarwin $
 */


/** Zend_View_Helper_FormRadio */
require_once 'Zend/View/Helper/FormRadio.php';


/**
 * Helper to generate a set of checkbox button elements
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Helpers_Forms_Decorator_FormMultiCheckbox extends Zend_View_Helper_FormRadio
{
    /**
     * Input type to use
     * @var string
     */
    protected $_inputType = 'checkbox';

    /**
     * Whether or not this element represents an array collection by default
     * @var bool
     */
    protected $_isArray = true;

    /**
     * Generates a set of checkbox button elements.
     *
     * @param $name
     * @param null $value
     * @param null $attribs
     * @param null $options
     * @param string $listsep
     * @return string
     */
    public function formMultiCheckbox($name, $value = null, $attribs = null, $options = null, $listsep = "<br />\n")
    {
        return $this->formRadio($name, $value, $attribs, $options, $listsep);
    }
}
