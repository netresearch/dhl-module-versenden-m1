<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Test_Model_Observer_ResetVersendenInfoTest
    extends EcomDev_PHPUnit_Test_Case
{


    /**
     * @test
     * @loadFixture Model_ObserverTest
     */
    public function resetServices()
    {
        $coreSessionMock = $this
            ->getMockBuilder('Mage_Core_Model_Session')
            ->setMethods(array('start'))
            ->getMock();
        $this->replaceByMock('singleton', 'core/session', $coreSessionMock);

        $quote       = Mage::getModel('sales/quote')->load(300);
        $sessionMock = $this->getModelMock('checkout/session', array('init', 'getQuote'));
        $sessionMock
            ->expects($this->any())
            ->method('getQuote')
            ->willReturn($quote);
        $this->replaceByMock('model', 'checkout/session', $sessionMock);

        $observer    = new Varien_Event_Observer();
        $dhlObserver = new Dhl_Versenden_Model_Observer();
        $dhlObserver->resetVersendenInfo($observer);

        /** @var \Dhl\Versenden\Bcs\Api\Info $versendenInfo */
        $versendenInfo = $quote->getShippingAddress()->getData('dhl_versenden_info');
        $this->assertNull($versendenInfo);
    }
}
