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
 * Dhl_Versenden_Model_Shipping_Carrier_Versenden
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Model_Shipping_Carrier_Versenden
    extends Mage_Shipping_Model_Carrier_Abstract
    implements Mage_Shipping_Model_Carrier_Interface
{
    const CODE = 'dhlversenden';

    /**
     * Init carrier code
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_code = self::CODE;
    }

    /**
     * The DHL Versenden carrier does not calculate rates.
     *
     * @param Mage_Shipping_Model_Rate_Request $request
     * @return null
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        return null;
    }

    /**
     * The DHL Versenden carrier does not introduce own methods.
     *
     * @return mixed[]
     */
    public function getAllowedMethods()
    {
        return array();
    }

    /**
     * @param string $type
     * @param string $code
     * @return bool|mixed
     */
    public function getCode($type, $code = '')
    {
        $codes = array(
            'unit_of_measure' => array(
                'G'   =>  Mage::helper('dhl_versenden')->__('Grams'),
                'KG'   =>  Mage::helper('dhl_versenden')->__('Kilograms'),
            ),
        );

        if (!isset($codes[$type])) {
            return false;
        } elseif ('' === $code) {
            return $codes[$type];
        }

        if (!isset($codes[$type][$code])) {
            return false;
        } else {
            return $codes[$type][$code];
        }
    }
}
