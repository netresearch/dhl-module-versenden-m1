<?php

/**
 * See LICENSE.md for license details.
 */

use Dhl\Versenden\ParcelDe\Service\Collection as ServiceCollection;
use Dhl\Versenden\ParcelDe\Service;

class Dhl_Versenden_Test_Model_ServiceTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @return Service\Type\Generic[]
     */
    protected function getCustomerServices()
    {
        return [
            new Service\PreferredDay('', true, false, ['']),
            new Service\ParcelAnnouncement('', Service\ParcelAnnouncement::DISPLAY_MODE_OPTIONAL, false),
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
            new Service\AdditionalInsurance('', true, false, ''),
            new Service\ReturnShipment('', true, false),
            new Service\VisualCheckOfAge(
                '',
                true,
                false,
                [
                    Service\VisualCheckOfAge::A16 => Service\VisualCheckOfAge::A16,
                    Service\VisualCheckOfAge::A18 => Service\VisualCheckOfAge::A18,
                ],
            ),
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

        array_walk(
            $customerServices,
            function (Service\Type\Generic $service) {
                $this->assertTrue($service->isCustomerService());
            },
        );
        array_walk(
            $merchantServices,
            function (Service\Type\Generic $service) {
                $this->assertFalse($service->isCustomerService());
            },
        );
    }

    /**
     * Make sure the services with frontend input type "select" have options.
     *
     * @test
     */
    public function selectHasOptions()
    {
        $services = $this->getServices();
        array_walk(
            $services,
            function (Service\Type\Generic $service) {
                if ($service instanceof Service\Type\Select) {
                    $serviceOptions = $service->getOptions();
                    $this->assertIsArray($serviceOptions);
                    $this->assertNotEmpty($serviceOptions, get_class($service));
                }
            },
        );
    }

    /**
     * @test
     */
    public function serviceCollection()
    {
        $collection       = new ServiceCollection();
        $customerServices = $this->getCustomerServices();
        $collection->setItems($customerServices);

        $getClassNames = function ($object) {
            return get_class($object);
        };

        // in === out
        $serviceClassNames    = array_map($getClassNames, $customerServices);
        $collectionClassNames = array_map($getClassNames, $collection->getItems());
        static::assertEmpty(array_diff($serviceClassNames, $collectionClassNames));

        // counter
        static::assertEquals(
            count($customerServices),
            count($collection),
        );

        // iterator
        foreach ($collection as $item) {
            static::assertInstanceOf(Service\Type\Generic::class, $item);
        }

        static::assertInstanceOf(
            Service\PreferredLocation::class,
            $collection->getItem('preferredLocation'),
        );

        static::assertNull($collection->getItem('foo'));
    }

    /**
     * @test
     */
    public function frontendOutputBoolean()
    {
        $boolService = new Service\ReturnShipment('', true, false);

        static::assertStringContainsString('type="checkbox"', $boolService->getSelectorHtml());
        static::assertStringContainsString('label for', $boolService->getLabelHtml());
        static::assertEquals('', $boolService->getValueHtml());
    }

    /**
     * @test
     */
    public function frontendOutputSelect()
    {
        $selectService = new Service\VisualCheckOfAge(
            '',
            true,
            false,
            [
                Service\VisualCheckOfAge::A16 => Service\VisualCheckOfAge::A16,
                Service\VisualCheckOfAge::A18 => Service\VisualCheckOfAge::A18,
            ],
        );

        // Select types have a checkbox to enable/disable the dropdown
        static::assertStringContainsString('type="checkbox"', $selectService->getSelectorHtml());
        static::assertStringContainsString('label for', $selectService->getLabelHtml());
        static::assertStringContainsString('select', $selectService->getValueHtml());
    }

    /**
     * @test
     */
    public function frontendOutputText()
    {
        $textService = new Service\PreferredNeighbour('', true, false, '');

        static::assertStringContainsString('type="checkbox"', $textService->getSelectorHtml());
        static::assertStringContainsString('label for', $textService->getLabelHtml());
        static::assertStringContainsString('type="text', $textService->getValueHtml());
    }

    /**
     * @test
     */
    public function rendererReadOnly()
    {
        // assert passthrough
        $boolService = new Service\ParcelAnnouncement('', Service\ParcelAnnouncement::DISPLAY_MODE_OPTIONAL, false);
        $renderer    = new Service\Type\Renderer($boolService);

        static::assertEquals($boolService->getSelectorHtml(), $renderer->getSelectorHtml());
        static::assertEquals($boolService->getLabelHtml(), $renderer->getLabelHtml());
        static::assertEquals($boolService->getValueHtml(), $renderer->getValueHtml());

        // assert readonly values, service not selected
        $yesValue = 'Foo';
        $noValue  = 'Bar';
        $renderer = new Service\Type\Renderer($boolService, true);
        $renderer->setSelectedYes($yesValue);
        $renderer->setSelectedNo($noValue);

        $readOnlyHtml = $renderer->getSelectorHtml();
        static::assertNotEquals($boolService->getSelectorHtml(), $readOnlyHtml);
        static::assertStringContainsString('disabled="disabled"', $readOnlyHtml);
        static::assertStringContainsString('data-locked="1"', $readOnlyHtml);
        static::assertStringContainsString('name="shipment_service[parcelAnnouncement]"', $readOnlyHtml);
        static::assertStringContainsString('value="parcelAnnouncement"', $readOnlyHtml);
        static::assertStringNotContainsString('checked="checked"', $readOnlyHtml);
        // Read-only label uses same <label> format as Boolean services
        static::assertEquals($boolService->getLabelHtml(), $renderer->getLabelHtml());
        static::assertNotEquals($boolService->getValueHtml(), $renderer->getValueHtml());
        static::assertEquals($noValue, $renderer->getValueHtml());

        // assert readonly values, service selected
        $boolService = new Service\ReturnShipment('', true, true);
        $renderer    = new Service\Type\Renderer($boolService, true);
        $renderer->setSelectedYes($yesValue);
        $renderer->setSelectedNo($noValue);

        $selectedHtml = $renderer->getSelectorHtml();
        static::assertNotEquals($boolService->getSelectorHtml(), $selectedHtml);
        static::assertStringContainsString('checked="checked"', $selectedHtml);
        static::assertStringContainsString('name="shipment_service[returnShipment]"', $selectedHtml);
        static::assertStringContainsString('value="returnShipment"', $selectedHtml);
        static::assertEquals($boolService->getLabelHtml(), $renderer->getLabelHtml());
        static::assertNotEquals($boolService->getValueHtml(), $renderer->getValueHtml());
        static::assertEquals($yesValue, $renderer->getValueHtml());

        // assert readonly values, service with details
        $neighbour   = 'Alf';
        $textService = new Service\PreferredNeighbour('', true, true, '');
        $textService->setValue($neighbour);
        $renderer = new Service\Type\Renderer($textService, true);

        static::assertNotEquals($boolService->getSelectorHtml(), $renderer->getSelectorHtml());
        static::assertNotEquals($boolService->getLabelHtml(), $renderer->getLabelHtml());
        static::assertNotEquals($boolService->getValueHtml(), $renderer->getValueHtml());
        static::assertEquals($neighbour, $renderer->getValueHtml());
    }

    /**
     * @test
     */
    public function typeTest()
    {
        // boolean
        $name       = 'Bool Foo';
        $isEnabled  = true;
        $isSelected = false;

        $service = new Service\BulkyGoods($name, $isEnabled, $isSelected);

        static::assertEquals(Service\BulkyGoods::CODE, $service->getCode());
        static::assertEquals('boolean', $service->getFrontendInputType());
        static::assertEquals($name, $service->getName());
        static::assertSame($isEnabled, $service->isEnabled());
        static::assertSame($isSelected, $service->isSelected());
        static::assertSame($isSelected, $service->getValue());
        static::assertFalse($service->isCustomerService());

        static::assertStringContainsString($service->getCode(), $service->getSelectorHtml());
        static::assertStringContainsString($service->getCode(), $service->getLabelHtml());
        static::assertEmpty($service->getValueHtml());

        // select
        $name       = 'Option Foo';
        $isEnabled  = true;
        $isSelected = true;
        $value      = 'bar';
        $options    = ['foo' => 'fox', 'bar' => 'baz'];

        $service = new Service\VisualCheckOfAge($name, $isEnabled, $isSelected, $options);
        $service->setValue($value);

        static::assertEquals(Service\VisualCheckOfAge::CODE, $service->getCode());
        static::assertEquals('select', $service->getFrontendInputType());
        static::assertEquals($name, $service->getName());
        static::assertSame($isEnabled, $service->isEnabled());
        static::assertSame($isSelected, $service->isSelected());
        static::assertSame($value, $service->getValue());
        static::assertFalse($service->isCustomerService());

        // Select types have a checkbox to enable/disable the dropdown
        static::assertStringContainsString('type="checkbox"', $service->getSelectorHtml());
        static::assertStringContainsString($service->getCode(), $service->getLabelHtml());
        static::assertStringContainsString('selected', $service->getValueHtml());

        // text
        $name        = 'Text Foo';
        $placeholder = 'XXX';
        $isEnabled   = true;
        $isSelected  = true;
        $value       = 'bar';

        $service = new Service\PreferredLocation($name, $isEnabled, $isSelected, $placeholder);
        $service->setValue($value);

        static::assertEquals(Service\PreferredLocation::CODE, $service->getCode());
        static::assertEquals('text', $service->getFrontendInputType());
        static::assertEquals($name, $service->getName());
        static::assertSame($isEnabled, $service->isEnabled());
        static::assertSame($isSelected, $service->isSelected());
        static::assertSame($value, $service->getValue());
        static::assertTrue($service->isCustomerService());

        static::assertStringContainsString($service->getCode(), $service->getSelectorHtml());
        static::assertStringContainsString($service->getCode(), $service->getLabelHtml());
        static::assertStringContainsString($value, $service->getValueHtml());
    }

    /**
     * Assert that from the given set of services only the compatible items remain in the collection.
     * @test
     */
    public function filter()
    {
        $enabledServices      = new Service\Collection($this->getServices());
        $shippingProducts     = [\Dhl\Versenden\ParcelDe\Product::CODE_KLEINPAKET];
        $isPostalFacility     = false;
        $onlyCustomerServices = false;
        $filter               = new Service\Filter($shippingProducts, $isPostalFacility, $onlyCustomerServices);

        $filteredCollection = $filter->filterServiceCollection($enabledServices);

        static::assertInstanceOf(Service\Collection::class, $filteredCollection);
        static::assertCount(4, $filteredCollection);
        static::assertInstanceOf(
            Service\ParcelAnnouncement::class,
            $filteredCollection->getItem(Service\ParcelAnnouncement::CODE),
        );
        static::assertInstanceOf(
            Service\PreferredLocation::class,
            $filteredCollection->getItem(Service\PreferredLocation::CODE),
        );
        static::assertInstanceOf(
            Service\PreferredNeighbour::class,
            $filteredCollection->getItem(Service\PreferredNeighbour::CODE),
        );
        static::assertInstanceOf(
            Service\ReturnShipment::class,
            $filteredCollection->getItem(Service\ReturnShipment::CODE),
        );
    }

    /**
     * @test
     */
    public function filterPostalFacility()
    {
        $enabledServices      = new Service\Collection($this->getServices());
        $shippingProducts     = [\Dhl\Versenden\ParcelDe\Product::CODE_PAKET_NATIONAL];
        $isPostalFacility     = true;
        $onlyCustomerServices = false;
        $filter               = new Service\Filter($shippingProducts, $isPostalFacility, $onlyCustomerServices);

        $filteredCollection = $filter->filterServiceCollection($enabledServices);

        static::assertInstanceOf(Service\Collection::class, $filteredCollection);
        static::assertCount(3, $filteredCollection);
        static::assertInstanceOf(
            Service\AdditionalInsurance::class,
            $filteredCollection->getItem(Service\AdditionalInsurance::CODE),
        );
        static::assertInstanceOf(
            Service\ParcelAnnouncement::class,
            $filteredCollection->getItem(Service\ParcelAnnouncement::CODE),
        );
        static::assertInstanceOf(
            Service\ReturnShipment::class,
            $filteredCollection->getItem(Service\ReturnShipment::CODE),
        );
    }

    /**
     * @test
     */
    public function filterCustomerServices()
    {
        $enabledServices      = new Service\Collection($this->getServices());
        $shippingProducts     = [\Dhl\Versenden\ParcelDe\Product::CODE_KURIER_WUNSCHZEIT];
        $isPostalFacility     = false;
        $onlyCustomerServices = true;
        $filter               = new Service\Filter($shippingProducts, $isPostalFacility, $onlyCustomerServices);

        $filteredCollection = $filter->filterServiceCollection($enabledServices);

        static::assertInstanceOf(Service\Collection::class, $filteredCollection);
        static::assertCount(1, $filteredCollection);
        static::assertInstanceOf(
            Service\ParcelAnnouncement::class,
            $filteredCollection->getItem(Service\ParcelAnnouncement::CODE),
        );
    }
}
