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
use \Dhl\Versenden\Shipment\Service\Collection as ServiceCollection;
use \Dhl\Versenden\Shipment\Service;
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
     * @return Service\Type\Generic[]
     */
    protected function getCustomerServices()
    {
        return [
            new Service\DayOfDelivery('', true, false, ''),
            new Service\DeliveryTimeFrame('', true, false, [
                '10001200' => '10:00 - 12:00',
                '12001400' => '12:00 - 14:00',
                '14001600' => '14:00 - 16:00',
                '16001800' => '16:00 - 18:00',
                '18002000' => '18:00 - 20:00',
                '19002100' => '19:00 - 21:00',
            ]),
            new Service\ParcelAnnouncement('', true, false),
            new Service\PreferredLocation('', true, false, ''),
            new Service\PreferredNeighbour('', true, false, ''),
        ];
    }

    /**
     * @return Service\Type\Generic[]
     */
    protected function getMerchantServices()
    {
        return [
            new Service\BulkyGoods('', true, false),
            new Service\Insurance('', true, false),
            new Service\ReturnShipment('', true, false),
            new Service\VisualCheckOfAge('', true, false, [
                Service\VisualCheckOfAge::A16 => Service\VisualCheckOfAge::A16,
                Service\VisualCheckOfAge::A18 => Service\VisualCheckOfAge::A18,
            ]),
        ];
    }

    /**
     * @return Service\Type\Generic[]
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

        array_walk($customerServices, function (Service\Type\Generic $service) {
            $this->assertTrue($service->isCustomerService(), get_class($service));
        });
        array_walk($merchantServices, function (Service\Type\Generic $service) {
            $this->assertFalse($service->isCustomerService(), get_class($service));
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
        array_walk($services, function (Service\Type\Generic $service) {
            if ($service instanceof Service\Type\Select) {
                $serviceOptions = $service->getOptions();
                $this->assertInternalType('array', $serviceOptions);
                $this->assertNotEmpty($serviceOptions, get_class($service));
            }
        });
    }

    /**
     * @test
     */
    public function serviceCollection()
    {
        $collection = new ServiceCollection();
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
            $this->assertInstanceOf(Service\Type\Generic::class, $item);
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
        $boolService = new Service\BulkyGoods('', true, false);

        $this->assertContains('type="checkbox"', $boolService->getSelectorHtml());
        $this->assertContains('label for', $boolService->getLabelHtml());
        $this->assertEquals('', $boolService->getValueHtml());
    }

    /**
     * @test
     */
    public function frontendOutputHidden()
    {
        $hiddenService = new Service\ParcelAnnouncement('', Service\ParcelAnnouncement::DISPLAY_MODE_REQUIRED, false);

        $this->assertContains('type="hidden"', $hiddenService->getSelectorHtml());
        $this->assertEmpty($hiddenService->getLabelHtml());
        $this->assertEquals('', $hiddenService->getValueHtml());
    }

    /**
     * @test
     */
    public function frontendOutputSelect()
    {
        $selectService = new Service\VisualCheckOfAge('', true, false, [
            Service\VisualCheckOfAge::A16 => Service\VisualCheckOfAge::A16,
            Service\VisualCheckOfAge::A18 => Service\VisualCheckOfAge::A18,
        ]);

        $this->assertContains('type="checkbox"', $selectService->getSelectorHtml());
        $this->assertContains('label for', $selectService->getLabelHtml());
        $this->assertContains('select', $selectService->getValueHtml());
    }

    /**
     * @test
     */
    public function frontendOutputText()
    {
        $textService   = new Service\PreferredNeighbour('', true, false, '');

        $this->assertContains('type="checkbox"', $textService->getSelectorHtml());
        $this->assertContains('label for', $textService->getLabelHtml());
        $this->assertContains('type="text', $textService->getValueHtml());
    }
}
