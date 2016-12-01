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
 * @author    Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */

/**
 * Dhl_Versenden_Block_Config_Service
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Block_Config_Service extends Mage_Core_Block_Template
{
    protected $_nameArray = array(
        'preferredDay'       => 'Preferred day',
        'preferredTime'      => 'Preferred time',
        'preferredLocation'  => 'Preferred location',
        'preferredNeighbour' => 'Preferred neighbor',
        'parcelAnnouncement' => 'Parcel announcement',
    );

    public function renderName($nameKey)
    {
        return $this->_nameArray[$nameKey];
    }

    public function renderDate($value)
    {
        /** @var Mage_Core_Model_Date $dateModel */
        $dateModel = Mage::getSingleton('core/date');

        $formatedDate = $dateModel->date("d.m.Y", $value);
        if (strpos(Mage::app()->getLocale()->getLocaleCode(), 'de_') === false) {
            $formatedDate = $dateModel->date("d/m/Y", $value);
        }

        return $formatedDate;
    }

    public function renderTime($value)
    {
        if (!ctype_digit($value) || strlen($value) <> 8) {
            return $value;
        }
        $timeValues = str_split($value, 2);
        $result     = $timeValues[0] . ' - ' . $timeValues[2];

        return Mage::helper('dhl_versenden/data')->__($result);
    }

    /**
     * check if there are services selected 
     *
     * @return bool
     */
    public function isServiceSelected()
    {
        $services          = $this->getData('services');
        $servicesArray     = $services->toArray();
        $filteredServices  = array_filter($servicesArray);

        return count($filteredServices) > 0 ? true : false;
    }
}
