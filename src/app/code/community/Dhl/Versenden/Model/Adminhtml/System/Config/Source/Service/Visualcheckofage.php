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
 * @author    Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */

/**
 * Dhl_Versenden_Model_Adminhtml_System_Config_Source_Service_Visualcheckofage
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Model_Adminhtml_System_Config_Source_Service_Visualcheckofage
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $optionsArray = array(
            array(
            'value' => 0,
            'label' => Mage::helper('dhl_versenden/data')->__('No')
            ),
            array(
                'value' => \Dhl\Versenden\Bcs\Api\Shipment\Service\VisualCheckOfAge::A16,
                'label' => \Dhl\Versenden\Bcs\Api\Shipment\Service\VisualCheckOfAge::A16,
            ),
            array(
                'value' => \Dhl\Versenden\Bcs\Api\Shipment\Service\VisualCheckOfAge::A18,
                'label' => \Dhl\Versenden\Bcs\Api\Shipment\Service\VisualCheckOfAge::A18,
            )
        );

        return $optionsArray;
    }


}
