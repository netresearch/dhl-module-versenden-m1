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
use \Dhl\Versenden\Service;
/**
 * Dhl_Versenden_Test_Model_ServiceTest
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Test_Model_ServiceTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @return Service[]
     */
    protected function getCustomerServices()
    {
        return [
            new Service\DayOfDelivery(),
            new Service\DeliveryTimeFrame(),
            new Service\ParcelAnnouncement(),
            new Service\PreferredLocation(),
            new Service\PreferredNeighbour(),
        ];
    }

    /**
     * @return Service[]
     */
    protected function getMerchantServices()
    {
        return [
            new Service\BulkyGoods(),
            new Service\Insurance(),
            new Service\ReturnShipment(),
            new Service\VisualCheckOfAge(),
        ];
    }

    /**
     * @return Service[]
     */
    protected function getServices()
    {
        return array_merge($this->getCustomerServices(), $this->getMerchantServices());
    }

    /**
     * Test who can order an additional service.
     *
     * @test
     */
    public function isCustomerService()
    {
        $customerServices = $this->getCustomerServices();
        $merchantServices = $this->getMerchantServices();

        array_walk($customerServices, function (Service $service) {
            $this->assertTrue($service->isCustomerService, get_class($service));
        });
        array_walk($merchantServices, function (Service $service) {
            $this->assertFalse($service->isCustomerService, get_class($service));
        });
    }

    /**
     * Make sure the services with frontend input type "select" have options.
     *
     * @test
     */
    public function selectHasOptions()
    {
        $services = $this->getServices();
        array_walk($services, function (Service $service) {
            $serviceOptions = $service->getOptions();
            $this->assertInternalType('array', $serviceOptions);
            if ($service->getFrontendInput() === Service::INPUT_TYPE_SELECT) {
                $this->assertNotEmpty($serviceOptions, get_class($service));
            } else {
                $this->assertEmpty($serviceOptions, get_class($service));
            }
        });
    }

    /**
     * @test
     */
    public function serviceCollection()
    {
        $collection = new Service\Collection();
        $customerServices = $this->getCustomerServices();
        $collection->setItems($customerServices);

        $getClassNames = function ($object) {
            return get_class($object);
        };

        // in === out
        $serviceClassNames = array_map($getClassNames, $customerServices);
        $collectionClassNames = array_map($getClassNames, $collection->getItems());
        $this->assertEmpty(array_diff($serviceClassNames, $collectionClassNames));

        // counter
        $this->assertEquals(
            count($customerServices),
            count($collection)
        );

        // iterator
        foreach ($collection as $item) {
            $this->assertInstanceOf(Service::class, $item);
        }

        $this->assertInstanceOf(
            Service\PreferredLocation::class,
            $collection->getItem('preferredLocation')
        );

        $this->assertNull($collection->getItem('foo'));
    }

    /**
     * @test
     */
    public function frontendOutputBoolean()
    {
        $boolService = new Service\BulkyGoods();

        $this->assertContains('type="checkbox"', $boolService->getRenderer()->getSelectorHtml());
        $this->assertContains('label for', $boolService->getRenderer()->getLabelHtml($boolService->name));
        $this->assertEquals('', $boolService->getRenderer()->getSettingsHtml());
    }

    /**
     * @test
     */
    public function frontendOutputHidden()
    {
        $hiddenService = new Service\ParcelAnnouncement();
        $hiddenService->setIsRequired();

        $this->assertContains('type="hidden"', $hiddenService->getRenderer()->getSelectorHtml());
        $this->assertEmpty($hiddenService->getRenderer()->getLabelHtml($hiddenService->name));
        $this->assertEquals('', $hiddenService->getRenderer()->getSettingsHtml());
    }

    /**
     * @test
     */
    public function frontendOutputSelect()
    {
        $selectService = new Service\VisualCheckOfAge();

        $this->assertContains('type="checkbox"', $selectService->getRenderer()->getSelectorHtml());
        $this->assertContains('label for', $selectService->getRenderer()->getLabelHtml($selectService->name));
        $this->assertContains('select', $selectService->getRenderer()->getSettingsHtml());
    }

    /**
     * @test
     */
    public function frontendOutputText()
    {
        $textService   = new Service\PreferredNeighbour();

        $this->assertContains('type="checkbox"', $textService->getRenderer()->getSelectorHtml());
        $this->assertContains('label for', $textService->getRenderer()->getLabelHtml($textService->name));
        $this->assertContains('type="text', $textService->getRenderer()->getSettingsHtml());
    }
}
