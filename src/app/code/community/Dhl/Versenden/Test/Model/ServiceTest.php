<?php

/**
 * See LICENSE.md for license details.
 */

use \Dhl\Versenden\Bcs\Api\Shipment\Service\Collection as ServiceCollection;
use \Dhl\Versenden\Bcs\Api\Shipment\Service;

class Dhl_Versenden_Test_Model_ServiceTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @return Service\Type\Generic[]
     */
    protected function getCustomerServices()
    {
        return array(
            new Service\PreferredDay('', true, false, array('')),
            new Service\ParcelAnnouncement('', Service\ParcelAnnouncement::DISPLAY_MODE_OPTIONAL, false),
            new Service\PreferredLocation('', true, false, ''),
            new Service\PreferredNeighbour('', true, false, ''),
        );
    }

    /**
     * @return Service\Type\Generic[]
     */
    protected function getMerchantServices()
    {
        return array(
            new Service\BulkyGoods('', true, false),
            new Service\Insurance('', true, false),
            new Service\ReturnShipment('', true, false),
            new Service\VisualCheckOfAge(
                '',
                true,
                false,
                array(
                    Service\VisualCheckOfAge::A16 => Service\VisualCheckOfAge::A16,
                    Service\VisualCheckOfAge::A18 => Service\VisualCheckOfAge::A18,
                )
            ),
        );
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
            function(Service\Type\Generic $service) {
                $this->assertTrue($service->isCustomerService());
            }
        );
        array_walk(
            $merchantServices,
            function(Service\Type\Generic $service) {
                $this->assertFalse($service->isCustomerService());
            }
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
            function(Service\Type\Generic $service) {
                if ($service instanceof Service\Type\Select) {
                    $serviceOptions = $service->getOptions();
                    $this->assertInternalType('array', $serviceOptions);
                    $this->assertNotEmpty($serviceOptions, get_class($service));
                }
            }
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

        $getClassNames = function($object) {
            return get_class($object);
        };

        // in === out
        $serviceClassNames    = array_map($getClassNames, $customerServices);
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
        $boolService = new Service\ReturnShipment('', true, false);

        $this->assertContains('type="checkbox"', $boolService->getSelectorHtml());
        $this->assertContains('label for', $boolService->getLabelHtml());
        $this->assertEquals('', $boolService->getValueHtml());
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
            array(
                Service\VisualCheckOfAge::A16 => Service\VisualCheckOfAge::A16,
                Service\VisualCheckOfAge::A18 => Service\VisualCheckOfAge::A18,
            )
        );

        $this->assertContains('type="checkbox"', $selectService->getSelectorHtml());
        $this->assertContains('label for', $selectService->getLabelHtml());
        $this->assertContains('select', $selectService->getValueHtml());
    }

    /**
     * @test
     */
    public function frontendOutputText()
    {
        $textService = new Service\PreferredNeighbour('', true, false, '');

        $this->assertContains('type="checkbox"', $textService->getSelectorHtml());
        $this->assertContains('label for', $textService->getLabelHtml());
        $this->assertContains('type="text', $textService->getValueHtml());
    }

    /**
     * @test
     */
    public function rendererReadOnly()
    {
        // assert passthrough
        $boolService = new Service\ParcelAnnouncement('', Service\ParcelAnnouncement::DISPLAY_MODE_OPTIONAL, false);
        $renderer    = new Service\Type\Renderer($boolService);

        $this->assertEquals($boolService->getSelectorHtml(), $renderer->getSelectorHtml());
        $this->assertEquals($boolService->getLabelHtml(), $renderer->getLabelHtml());
        $this->assertEquals($boolService->getValueHtml(), $renderer->getValueHtml());

        // assert readonly values, service not selected
        $yesValue = 'Foo';
        $noValue  = 'Bar';
        $renderer = new Service\Type\Renderer($boolService, true);
        $renderer->setSelectedYes($yesValue);
        $renderer->setSelectedNo($noValue);

        $this->assertNotEquals($boolService->getSelectorHtml(), $renderer->getSelectorHtml());
        $this->assertNotEquals($boolService->getLabelHtml(), $renderer->getLabelHtml());
        $this->assertNotEquals($boolService->getValueHtml(), $renderer->getValueHtml());
        $this->assertEquals($noValue, $renderer->getValueHtml());

        // assert readonly values, service not selected
        $boolService = new Service\Insurance('', true, true);
        $renderer    = new Service\Type\Renderer($boolService, true);
        $renderer->setSelectedYes($yesValue);
        $renderer->setSelectedNo($noValue);

        $this->assertNotEquals($boolService->getSelectorHtml(), $renderer->getSelectorHtml());
        $this->assertNotEquals($boolService->getLabelHtml(), $renderer->getLabelHtml());
        $this->assertNotEquals($boolService->getValueHtml(), $renderer->getValueHtml());
        $this->assertEquals($yesValue, $renderer->getValueHtml());

        // assert readonly values, service with details
        $neighbour   = 'Alf';
        $textService = new Service\PreferredNeighbour('', true, true, '');
        $textService->setValue($neighbour);
        $renderer = new Service\Type\Renderer($textService, true);

        $this->assertNotEquals($boolService->getSelectorHtml(), $renderer->getSelectorHtml());
        $this->assertNotEquals($boolService->getLabelHtml(), $renderer->getLabelHtml());
        $this->assertNotEquals($boolService->getValueHtml(), $renderer->getValueHtml());
        $this->assertEquals($neighbour, $renderer->getValueHtml());
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

        $this->assertEquals(Service\BulkyGoods::CODE, $service->getCode());
        $this->assertEquals('boolean', $service->getFrontendInputType());
        $this->assertEquals($name, $service->getName());
        $this->assertSame($isEnabled, $service->isEnabled());
        $this->assertSame($isSelected, $service->isSelected());
        $this->assertSame($isSelected, $service->getValue());
        $this->assertFalse($service->isCustomerService());

        $this->assertContains($service->getCode(), $service->getSelectorHtml());
        $this->assertContains($service->getCode(), $service->getLabelHtml());
        $this->assertEmpty($service->getValueHtml());

        // select
        $name       = 'Option Foo';
        $isEnabled  = true;
        $isSelected = true;
        $value      = 'bar';
        $options    = array('foo' => 'fox', 'bar' => 'baz');

        $service = new Service\VisualCheckOfAge($name, $isEnabled, $isSelected, $options);
        $service->setValue($value);

        $this->assertEquals(Service\VisualCheckOfAge::CODE, $service->getCode());
        $this->assertEquals('select', $service->getFrontendInputType());
        $this->assertEquals($name, $service->getName());
        $this->assertSame($isEnabled, $service->isEnabled());
        $this->assertSame($isSelected, $service->isSelected());
        $this->assertSame($value, $service->getValue());
        $this->assertFalse($service->isCustomerService());

        $this->assertContains($service->getCode(), $service->getSelectorHtml());
        $this->assertContains($service->getCode(), $service->getLabelHtml());
        $this->assertContains('selected', $service->getValueHtml());

        // text
        $name        = 'Text Foo';
        $placeholder = 'XXX';
        $isEnabled   = true;
        $isSelected  = true;
        $value       = 'bar';

        $service = new Service\PreferredLocation($name, $isEnabled, $isSelected, $placeholder);
        $service->setValue($value);

        $this->assertEquals(Service\PreferredLocation::CODE, $service->getCode());
        $this->assertEquals('text', $service->getFrontendInputType());
        $this->assertEquals($name, $service->getName());
        $this->assertSame($isEnabled, $service->isEnabled());
        $this->assertSame($isSelected, $service->isSelected());
        $this->assertSame($value, $service->getValue());
        $this->assertTrue($service->isCustomerService());

        $this->assertContains($service->getCode(), $service->getSelectorHtml());
        $this->assertContains($service->getCode(), $service->getLabelHtml());
        $this->assertContains($value, $service->getValueHtml());
    }

    /**
     * Assert that from the given set of services only the compatible items remain in the collection.
     * @test
     */
    public function filter()
    {
        $enabledServices      = new Service\Collection($this->getServices());
        $shippingProducts     = array(\Dhl\Versenden\Bcs\Api\Product::CODE_KLEINPAKET);
        $isPostalFacility     = false;
        $onlyCustomerServices = false;
        $filter               = new Service\Filter($shippingProducts, $isPostalFacility, $onlyCustomerServices);

        $filteredCollection = $filter->filterServiceCollection($enabledServices);

        $this->assertInstanceOf(Service\Collection::class, $filteredCollection);
        $this->assertCount(3, $filteredCollection);
        $this->assertInstanceOf(
            Service\ParcelAnnouncement::class,
            $filteredCollection->getItem(Service\ParcelAnnouncement::CODE)
        );
        $this->assertInstanceOf(
            Service\PreferredLocation::class,
            $filteredCollection->getItem(Service\PreferredLocation::CODE)
        );
        $this->assertInstanceOf(
            Service\PreferredNeighbour::class,
            $filteredCollection->getItem(Service\PreferredNeighbour::CODE)
        );
    }

    /**
     * @test
     */
    public function filterPostalFacility()
    {
        $enabledServices      = new Service\Collection($this->getServices());
        $shippingProducts     = array(\Dhl\Versenden\Bcs\Api\Product::CODE_PAKET_NATIONAL);
        $isPostalFacility     = true;
        $onlyCustomerServices = false;
        $filter               = new Service\Filter($shippingProducts, $isPostalFacility, $onlyCustomerServices);

        $filteredCollection = $filter->filterServiceCollection($enabledServices);

        $this->assertInstanceOf(Service\Collection::class, $filteredCollection);
        $this->assertCount(3, $filteredCollection);
        $this->assertInstanceOf(
            Service\Insurance::class,
            $filteredCollection->getItem(Service\Insurance::CODE)
        );
        $this->assertInstanceOf(
            Service\ParcelAnnouncement::class,
            $filteredCollection->getItem(Service\ParcelAnnouncement::CODE)
        );
        $this->assertInstanceOf(
            Service\ReturnShipment::class,
            $filteredCollection->getItem(Service\ReturnShipment::CODE)
        );
    }

    /**
     * @test
     */
    public function filterCustomerServices()
    {
        $enabledServices      = new Service\Collection($this->getServices());
        $shippingProducts     = array(\Dhl\Versenden\Bcs\Api\Product::CODE_KURIER_WUNSCHZEIT);
        $isPostalFacility     = false;
        $onlyCustomerServices = true;
        $filter               = new Service\Filter($shippingProducts, $isPostalFacility, $onlyCustomerServices);

        $filteredCollection = $filter->filterServiceCollection($enabledServices);

        $this->assertInstanceOf(Service\Collection::class, $filteredCollection);
        $this->assertCount(1, $filteredCollection);
        $this->assertInstanceOf(
            Service\ParcelAnnouncement::class,
            $filteredCollection->getItem(Service\ParcelAnnouncement::CODE)
        );
    }
}
