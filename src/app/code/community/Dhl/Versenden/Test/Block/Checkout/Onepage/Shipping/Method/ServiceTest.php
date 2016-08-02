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
 * Dhl_Versenden_Test_Block_Checkout_Onepage_Shipping_Method_ServiceTest
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Test_Block_Checkout_Onepage_Shipping_Method_ServiceTest
    extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     */
    public function getServices()
    {
        $serviceOne = new \Dhl\Versenden\Service\Type\BulkyGoods();
        $serviceTwo = new \Dhl\Versenden\Service\Type\PreferredLocation();
        $collection = new \Dhl\Versenden\Service\Collection([
            $serviceOne, $serviceTwo
        ]);

        $configMock = $this->getModelMock('dhl_versenden/config', ['getEnabledServices']);
        $configMock
            ->expects($this->once())
            ->method('getEnabledServices')
            ->willReturn($collection);
        $this->replaceByMock('model', 'dhl_versenden/config', $configMock);

        $block = Mage::app()->getLayout()->createBlock('dhl_versenden/checkout_onepage_shipping_method_service');

        $frontendServices = $block->getServices();
        $this->assertInternalType('array', $frontendServices);
        $this->assertCount(1, $frontendServices);
        $this->assertContains($serviceTwo, $frontendServices);
    }
}
