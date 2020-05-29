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
 * Dhl_Versenden_Test_Model_Observer_CodPaymentTest
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Test_Model_Observer_CodPaymentTest
    extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     */
    public function disableCodPaymentNoQuote()
    {
        $checkResult = new stdClass();
        $checkResult->isAvailable = true;

        $observer = new Varien_Event_Observer();
        $observer->setData('result', $checkResult);

        $sessionMock = $this->getModelMock(
            'checkout/session',
            array('getQuote'),
            false,
            array(),
            '',
            false
        );
        $sessionMock
            ->expects($this->once())
            ->method('getQuote')
            ->willReturn(null);
        $this->replaceByMock('singleton', 'checkout/session', $sessionMock);

        $dhlObserver = new Dhl_Versenden_Model_Observer();
        $dhlObserver->disableCodPayment($observer);
    }

    /**
     * @test
     * @loadFixture Model_ShipmentConfigTest
     */
    public function disableCodPaymentWrongShippingMethod()
    {
        $methodInstance = new Mage_Payment_Model_Method_Cashondelivery();

        $shippingMethod  = 'foo_bar';
        $shippingAddress = new Mage_Sales_Model_Quote_Address();
        $shippingAddress->setShippingMethod($shippingMethod);
        $quote = new Mage_Sales_Model_Quote();
        $quote->setStoreId(1);
        $quote->setShippingAddress($shippingAddress);

        $checkResult = new stdClass();
        $checkResult->isAvailable = true;

        $observer = new Varien_Event_Observer();
        $observer->setData('result', $checkResult);
        $observer->setData('quote', $quote);
        $observer->setData('method_instance', $methodInstance);

        $configMock = $this->getModelMock('shipping/config', array('getCarrierInstance'));
        $configMock
            ->expects($this->never())
            ->method('getCarrierInstance');
        $this->replaceByMock('model', 'shipping/config', $configMock);

        $dhlObserver = new Dhl_Versenden_Model_Observer();
        $dhlObserver->disableCodPayment($observer);
    }

    /**
     * @test
     * @loadFixture Model_ShipmentConfigTest
     */
    public function disableCodPaymentWrongPaymentMethod()
    {
        $methodInstance = new Mage_Payment_Model_Method_Checkmo();

        $shippingMethod  = 'flatrate_flatrate';
        $shippingAddress = new Mage_Sales_Model_Quote_Address();
        $shippingAddress->setShippingMethod($shippingMethod);
        $quote = new Mage_Sales_Model_Quote();
        $quote->setStoreId(1);
        $quote->setShippingAddress($shippingAddress);

        $checkResult = new stdClass();
        $checkResult->isAvailable = true;

        $observer = new Varien_Event_Observer();
        $observer->setData('result', $checkResult);
        $observer->setData('quote', $quote);
        $observer->setData('method_instance', $methodInstance);

        $configMock = $this->getModelMock('shipping/config', array('getCarrierInstance'));
        $configMock
            ->expects($this->never())
            ->method('getCarrierInstance');
        $this->replaceByMock('model', 'shipping/config', $configMock);

        $dhlObserver = new Dhl_Versenden_Model_Observer();
        $dhlObserver->disableCodPayment($observer);
    }

    /**
     * @test
     * @loadFixture Model_ShipmentConfigTest
     */
    public function disableCodPaymentCodAllowed()
    {
        $isAvailable = 'foo';
        $methodInstance = new Mage_Payment_Model_Method_Cashondelivery();

        $shippingMethod  = 'flatrate_flatrate';
        $shippingAddress = new Mage_Sales_Model_Quote_Address();
        $shippingAddress->setShippingMethod($shippingMethod);
        $shippingAddress->setCountryId('DE');
        $quote = new Mage_Sales_Model_Quote();
        $quote->setStoreId(1);
        $quote->setShippingAddress($shippingAddress);

        $checkResult = new stdClass();
        $checkResult->isAvailable = $isAvailable;

        $observer = new Varien_Event_Observer();
        $observer->setData('result', $checkResult);
        $observer->setData('quote', $quote);
        $observer->setData('method_instance', $methodInstance);

        $dhlObserver = new Dhl_Versenden_Model_Observer();
        $dhlObserver->disableCodPayment($observer);

        $this->assertEquals($isAvailable, $observer->getData('result')->isAvailable);
    }

    /**
     * @test
     * @loadFixture Model_ShipmentConfigTest
     */
    public function disableCodPaymentCodNotAllowed()
    {
        $isAvailable = 'foo';
        $methodInstance = new Mage_Payment_Model_Method_Cashondelivery();

        $shippingMethod  = 'flatrate_flatrate';
        $shippingAddress = new Mage_Sales_Model_Quote_Address();
        $shippingAddress->setShippingMethod($shippingMethod);
        $shippingAddress->setCountryId('NZ');
        $quote = new Mage_Sales_Model_Quote();
        $quote->setStoreId(1);
        $quote->setShippingAddress($shippingAddress);

        $checkResult = new stdClass();
        $checkResult->isAvailable = $isAvailable;

        $observer = new Varien_Event_Observer();
        $observer->setData('result', $checkResult);
        $observer->setData('quote', $quote);
        $observer->setData('method_instance', $methodInstance);

        $dhlObserver = new Dhl_Versenden_Model_Observer();
        $dhlObserver->disableCodPayment($observer);

        $this->assertFalse($observer->getData('result')->isAvailable);
    }
}
