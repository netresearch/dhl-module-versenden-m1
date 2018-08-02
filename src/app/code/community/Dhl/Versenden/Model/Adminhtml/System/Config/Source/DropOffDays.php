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
 * @author    Andreas Müller <andreas.mueller@netresearch.de>
 * @copyright 2018 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://www.netresearch.de/
 */

/**
 * Dhl_Versenden_Model_Adminhtml_System_Config_Source_DropOffDays
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Andreas Müller <andreas.mueller@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     https://www.netresearch.de/
 */
class Dhl_Versenden_Model_Adminhtml_System_Config_Source_DropOffDays
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $days = array(
            '1' => Mage::helper('dhl_versenden/data')->__('Mon'),
            '2' => Mage::helper('dhl_versenden/data')->__('Tue'),
            '3' => Mage::helper('dhl_versenden/data')->__('Wed'),
            '4' => Mage::helper('dhl_versenden/data')->__('Thu'),
            '5' => Mage::helper('dhl_versenden/data')->__('Fri'),
            '6' => Mage::helper('dhl_versenden/data')->__('Sat')
        );

        return $days;
    }
}
