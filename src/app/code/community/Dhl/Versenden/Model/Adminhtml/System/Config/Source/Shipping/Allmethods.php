<?php
/**
 * Dhl Versenden
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to
 * newer versions in the future.
 *
 * PHP version 5
 *
 * @category  Dhl
 * @package   Dhl_Versenden
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */

/**
 * Dhl_Versenden_Model_Adminhtml_System_Config_Source_Shipping_Allmethods
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Model_Adminhtml_System_Config_Source_Shipping_Allmethods
    extends Mage_Adminhtml_Model_System_Config_Source_Shipping_Allmethods
{
    /**
     * The core source model changes the meaning of the incoming parameter.
     *
     * @see Mage_Adminhtml_Block_System_Config_Form::initFields()
     * @see Mage_Adminhtml_Model_System_Config_Source_Shipping_Allmethods::toOptionArray()
     * @param bool $isMultiSelect
     * @return mixed[]
     */
    public function toOptionArray($isMultiSelect = false)
    {
        $showActiveOnlyFlag = $isMultiSelect;
        return parent::toOptionArray($showActiveOnlyFlag);
    }
}
