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
 * Dhl_Versenden_Test_Model_Observer_ResetVersendenInfoTest
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
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
