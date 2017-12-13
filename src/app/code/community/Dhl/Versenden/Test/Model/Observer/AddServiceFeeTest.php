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
 * Dhl_Versenden_Test_Model_Observer_AddServiceFeeTest
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
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
