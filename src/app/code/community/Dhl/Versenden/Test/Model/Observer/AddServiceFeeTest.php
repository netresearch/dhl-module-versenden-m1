<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Test_Model_Observer_AddServiceFeeTest
    extends EcomDev_PHPUnit_Test_Case
{

    protected function setUp()
    {
        parent::setUp();
        $coreSessionMock = $this
            ->getMockBuilder('Mage_Core_Model_Session')
            ->setMethods(array('start'))
            ->getMock();
        $this->replaceByMock('singleton', 'core/session', $coreSessionMock);
        $this->setCurrentStore('store_one');
    }


    /**
     * @test
     * @loadFixture Model_ObserverTest
     */
    public function addServiceFeeNoVersendenInfo()
    {
        $quote       = Mage::getModel('sales/quote')->load(100);
        $observer    = new Varien_Event_Observer();
        $observer->setData('quote', $quote);
        $dhlObserver = new Dhl_Versenden_Model_Observer();
        $dhlObserver->addServiceFee($observer);
        $this->assertNull($quote->getShippingAddress()->getData('dhl_versenden_info'));
    }

    /**
     * @test
     * @loadFixture Model_ObserverTest
     */
    public function addServiceFee()
    {

        $quote       = Mage::getModel('sales/quote')->load(300);
        $observer    = new Varien_Event_Observer();
        $observer->setData('quote', $quote);
        $dhlObserver = new Dhl_Versenden_Model_Observer();
        $dhlObserver->addServiceFee($observer);
        $this->assertNull($quote->getShippingAddress()->getData('dhl_versenden_info'));
    }
}
