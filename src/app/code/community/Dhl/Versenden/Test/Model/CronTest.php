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
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */

/**
 * Dhl_Versenden_Test_Model_CronTest
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Test_Model_CronTest extends EcomDev_PHPUnit_Test_Case
{
    protected function setUp()
    {
        parent::setUp();

        $writerMock = $this->getModelMock('dhl_versenden/logger_writer', array('log', 'logException'));
        $this->replaceByMock('singleton', 'dhl_versenden/logger_writer', $writerMock);
    }

    /**
     * @test
     * @loadFixture Model_ShipperConfigTest
     * @loadFixture Model_AutoCreateTest
     * @loadFixture Model_AutoCreateFailureTest
     */
    public function shipmentAutoCreateFailure()
    {
        $schedule = Mage::getModel('cron/schedule');
        /** @var Dhl_Versenden_Model_Cron $cron */
        $cron = Mage::getModel('dhl_versenden/cron');
        $cron->shipmentAutoCreate($schedule);

        $this->assertEquals(Mage_Cron_Model_Schedule::STATUS_ERROR, $schedule->getStatus());
    }

    /**
     * @test
     * @loadFixture Model_ShipperConfigTest
     * @loadFixture Model_AutoCreateTest
     */
    public function shipmentAutoCreateSuccess()
    {
        $this->markTestIncomplete('Test must create a shipment in order to be recognized as success.');

        $numVersendenOrders = 1;
        $numSuccess = 1;

        $autocreateMock = $this->getModelMock(
            'dhl_versenden/shipping_autocreate',
            array('autoCreate'),
            false,
            array(),
            '',
            false
        );
        $autocreateMock
            ->expects($this->once())
            ->method('autoCreate')
            ->willReturn($numSuccess);
        $this->replaceByMock('model', 'dhl_versenden/shipping_autocreate', $autocreateMock);

        $schedule = Mage::getModel('cron/schedule');

        /** @var Dhl_Versenden_Model_Cron $cron */
        $cron = Mage::getModel('dhl_versenden/cron');
        $cron->shipmentAutoCreate($schedule);

        $format = Dhl_Versenden_Model_Cron::CRON_MESSAGE_LABELS_RETRIEVED;
        $expectedMessage = sprintf($format, $numSuccess, $numVersendenOrders);

        $this->assertEquals(Mage_Cron_Model_Schedule::STATUS_SUCCESS, $schedule->getStatus());
        $this->assertEquals($expectedMessage, $schedule->getMessages());
    }
}
