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
 * Dhl_Versenden_Test_Model_Observer_CheckoutTest
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Test_Model_Observer_CheckoutProgressTest
    extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function appendServicesToShippingMethod()
    {
        $this->setCurrentStore('store_two');

        $modelMock =
            $this->getModelMock('checkout/session', array('init', 'getQuote', 'getShippingAddress', 'getData'));
        $modelMock->expects($this->any())->method('getQuote')->willReturnSelf();
        $modelMock->expects($this->any())->method('getShippingAddress')->willReturnSelf();
        $modelMock
            ->expects($this->any())
            ->method('getData')
            ->willReturn(
                '{"schemaVersion":"1.0","receiver":{"packstation":{"packstationNumber":null,"postNumber":null,"zip":null,"city":null,"country":null,"countryISOCode":null,"state":null},"postfiliale":{"postfilialNumber":null,"postNumber":null,"zip":null,"city":null,"country":null,"countryISOCode":null,"state":null},"parcelShop":{"parcelShopNumber":null,"streetName":null,"streetNumber":null,"zip":null,"city":null,"country":null,"countryISOCode":null,"state":null},"name1":"Max Mustermann","name2":null,"name3":null,"streetName":"Musterstrasse","streetNumber":"15a","addressAddition":"","dispatchingInformation":null,"zip":"49084","city":"Mustertown","country":"Deutschland","countryISOCode":"DE","state":null,"phone":"3324242","email":"test@tester.de","contactPerson":null},"services":{"preferredDay":"2016-11-12","preferredTime":"12001400","preferredLocation":"testt","preferredNeighbour":null,"parcelAnnouncement":null,"visualCheckOfAge":null,"returnShipment":null,"insurance":null,"bulkyGoods":null,"cod":null,"printOnlyIfCodeable":null}}'
            );

        $this->replaceByMock('model', 'checkout/session', $modelMock);

        $observer  = new Varien_Event_Observer();
        $blockHtml = '<dd>foo</dd>';
        /** @var Mage_Checkout_Block_Onepage_Progress $block */
        $block = new Mage_Checkout_Block_Onepage_Progress();
        $block->setLayout(Mage::app()->getLayout());
        $block->getLayout()->getUpdate()->addHandle('checkout_onepage_progress_shipping_method');

        $transport = new Varien_Object();
        $transport->setData('html', $blockHtml);

        $observer->setData('block', $block);
        $observer->setData('transport', $transport);

        $dhlObserver = new Dhl_Versenden_Model_Observer();
        $dhlObserver->appendServicesToShippingMethod($observer);

        $this->assertContains('Preferred Day', $transport->getData('html'));
        $this->assertContains('12 - 14', $transport->getData('html'));
    }
}
