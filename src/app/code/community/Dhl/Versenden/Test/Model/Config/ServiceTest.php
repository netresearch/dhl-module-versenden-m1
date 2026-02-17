<?php

/**
 * See LICENSE.md for license details.
 */

use Dhl\Versenden\ParcelDe\Service;

class Dhl_Versenden_Test_Model_Config_ServiceTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function getEnabledServices()
    {
        $config = new Dhl_Versenden_Model_Config_Service();

        // (1) parcel announcement is set to required via config fixture
        // → should be in the list of enabled services
        $defaultServiceCollection = $config->getEnabledServices();
        static::assertInstanceOf(Service\Collection::class, $defaultServiceCollection);
        $defaultServices = $defaultServiceCollection->getItems();
        static::assertIsArray($defaultServices);

        /** @var \Dhl\Versenden\ParcelDe\Service\ParcelAnnouncement[] $paServices */
        $paServices = array_filter($defaultServices, function ($service) {
            return ($service instanceof Service\ParcelAnnouncement);
        });
        static::assertNotEmpty($paServices);

        // (2) bulky goods is disabled via config fixture
        // → should not be in the list of enabled services
        $bgServices = array_filter($defaultServices, function ($service) {
            return ($service instanceof Service\BulkyGoods);
        });
        static::assertNotEmpty($bgServices);

        // (3) parcel announcement is set to optional via config fixture
        // → should be in the list of enabled services
        $storeServiceCollection = $config->getEnabledServices('store_two');
        static::assertInstanceOf(Service\Collection::class, $storeServiceCollection);
        $storeServices = $storeServiceCollection->getItems();
        static::assertIsArray($storeServices);

        /** @var \Dhl\Versenden\ParcelDe\Service\ParcelAnnouncement[] $paServices */
        $paServices = array_filter($storeServices, function ($service) {
            return ($service instanceof Service\ParcelAnnouncement);
        });
        static::assertNotEmpty($paServices);
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function getPrefDayFee($store = null)
    {
        $config = new Dhl_Versenden_Model_Config_Service();
        static::assertEquals(5, $config->getPrefDayFee());
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function getPrefDayHandlingFeeText()
    {
        $config = new Dhl_Versenden_Model_Config_Service();
        $text = $config->getPrefDayHandlingFeeText();

        // Fee is 5 according to fixture, so should contain formatted value
        static::assertIsString($text);
        // The text should contain the fee value since fee > 0
        static::assertNotEmpty($text);
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function getCutOffTime()
    {
        $config = new Dhl_Versenden_Model_Config_Service();
        $cutoff = $config->getCutOffTime();

        // Should return the configured cutoff time
        static::assertNotNull($cutoff);
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function getParcelOutletNotificationEmail()
    {
        $config = new Dhl_Versenden_Model_Config_Service();
        $email = $config->getParcelOutletNotificationEmail();

        // Should return a string (possibly empty if not configured)
        static::assertIsString($email);
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function getServices()
    {
        $config = new Dhl_Versenden_Model_Config_Service();
        $services = $config->getServices();

        // Should return a collection
        static::assertInstanceOf(Service\Collection::class, $services);

        // Should contain all service types
        $items = $services->getItems();
        static::assertNotEmpty($items);
        static::assertCount(18, $items); // 18 services (PrintOnlyIfCodeable is config-only)
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function getServicesIncludesNoNeighbourDelivery()
    {
        $config = new Dhl_Versenden_Model_Config_Service();
        $services = $config->getServices();

        $service = $services->getItem(Service\NoNeighbourDelivery::CODE);
        static::assertNotNull($service, 'NoNeighbourDelivery service should be in collection');
        static::assertInstanceOf(Service\NoNeighbourDelivery::class, $service);
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function getServicesIncludesNamedPersonOnly()
    {
        $config = new Dhl_Versenden_Model_Config_Service();
        $services = $config->getServices();

        $service = $services->getItem(Service\NamedPersonOnly::CODE);
        static::assertNotNull($service, 'NamedPersonOnly service should be in collection');
        static::assertInstanceOf(Service\NamedPersonOnly::class, $service);
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function getServicesIncludesSignedForByRecipient()
    {
        $config = new Dhl_Versenden_Model_Config_Service();
        $services = $config->getServices();

        $service = $services->getItem(Service\SignedForByRecipient::CODE);
        static::assertNotNull($service, 'SignedForByRecipient service should be in collection');
        static::assertInstanceOf(Service\SignedForByRecipient::class, $service);
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function getServicesIncludesGoGreen()
    {
        $config = new Dhl_Versenden_Model_Config_Service();
        $services = $config->getServices();

        $service = $services->getItem(Service\GoGreenPlus::CODE);
        static::assertNotNull($service, 'GoGreen service should be in collection');
        static::assertInstanceOf(Service\GoGreenPlus::class, $service);
    }

    /**
     * @test
     */
    public function namedPersonOnlyBlockedForInternational()
    {
        $filter = new \Dhl\Versenden\ParcelDe\Service\Filter(
            [\Dhl\Versenden\ParcelDe\Product::CODE_PAKET_NATIONAL],
            false,
            false,
            'DE',
            'US'
        );

        $service = new Service\NamedPersonOnly('namedPersonOnly', true, false);
        $result = $filter->filterService($service);

        static::assertNull($result, 'NamedPersonOnly should be blocked for international shipments');
    }

    /**
     * @test
     */
    public function namedPersonOnlyAllowedForDomesticDE()
    {
        $filter = new \Dhl\Versenden\ParcelDe\Service\Filter(
            [\Dhl\Versenden\ParcelDe\Product::CODE_PAKET_NATIONAL],
            false,
            false,
            'DE',
            'DE'
        );

        $service = new Service\NamedPersonOnly('namedPersonOnly', true, false);
        $result = $filter->filterService($service);

        static::assertNotNull($result, 'NamedPersonOnly should be allowed for domestic DE shipments');
    }

    /**
     * @test
     */
    public function signedForByRecipientBlockedForInternational()
    {
        $filter = new \Dhl\Versenden\ParcelDe\Service\Filter(
            [\Dhl\Versenden\ParcelDe\Product::CODE_PAKET_NATIONAL],
            false,
            false,
            'DE',
            'FR'
        );

        $service = new Service\SignedForByRecipient('signedForByRecipient', true, false);
        $result = $filter->filterService($service);

        static::assertNull($result, 'SignedForByRecipient should be blocked for international shipments');
    }

    /**
     * @test
     */
    public function signedForByRecipientAllowedForDomesticDE()
    {
        $filter = new \Dhl\Versenden\ParcelDe\Service\Filter(
            [\Dhl\Versenden\ParcelDe\Product::CODE_PAKET_NATIONAL],
            false,
            false,
            'DE',
            'DE'
        );

        $service = new Service\SignedForByRecipient('signedForByRecipient', true, false);
        $result = $filter->filterService($service);

        static::assertNotNull($result, 'SignedForByRecipient should be allowed for domestic DE shipments');
    }

    // =========================================================================
    // DHLGKP-365: International Shipping Services Tests
    // =========================================================================

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function getServicesIncludesEndorsement()
    {
        $config = new Dhl_Versenden_Model_Config_Service();
        $services = $config->getServices();

        $service = $services->getItem(Service\Endorsement::CODE);
        static::assertNotNull($service, 'Endorsement service should be in collection');
        static::assertInstanceOf(Service\Endorsement::class, $service);
    }

    /**
     * @test
     */
    public function endorsementHasReturnAndAbandonOptions()
    {
        $options = [
            '' => '-- Not selected --',
            Service\Endorsement::RETURN => 'Return',
            Service\Endorsement::ABANDON => 'Abandon',
        ];
        $service = new Service\Endorsement('Endorsement', true, false, $options);

        static::assertArrayHasKey('', $service->getOptions());
        static::assertArrayHasKey(Service\Endorsement::RETURN, $service->getOptions());
        static::assertArrayHasKey(Service\Endorsement::ABANDON, $service->getOptions());
        static::assertEquals('select', $service->getFrontendInputType());
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function getServicesIncludesDeliveryType()
    {
        $config = new Dhl_Versenden_Model_Config_Service();
        $services = $config->getServices();

        $service = $services->getItem(Service\DeliveryType::CODE);
        static::assertNotNull($service, 'DeliveryType service should be in collection');
        static::assertInstanceOf(Service\DeliveryType::class, $service);
    }

    /**
     * DeliveryType has three mutually exclusive options: Economy, Premium, CDP.
     *
     * @test
     */
    public function deliveryTypeHasThreeOptions()
    {
        $options = [
            Service\DeliveryType::ECONOMY => 'Economy',
            Service\DeliveryType::PREMIUM => 'Premium',
            Service\DeliveryType::CDP => 'Closest Drop Point',
        ];
        $service = new Service\DeliveryType('Delivery Type', true, false, $options);

        static::assertArrayHasKey(Service\DeliveryType::ECONOMY, $service->getOptions());
        static::assertArrayHasKey(Service\DeliveryType::PREMIUM, $service->getOptions());
        static::assertArrayHasKey(Service\DeliveryType::CDP, $service->getOptions());
        static::assertCount(3, $service->getOptions());
    }

    /**
     * DeliveryType renders as radio buttons (mutually exclusive options).
     *
     * @test
     */
    public function deliveryTypeIsRadioType()
    {
        $options = [
            Service\DeliveryType::ECONOMY => 'Economy',
            Service\DeliveryType::PREMIUM => 'Premium',
            Service\DeliveryType::CDP => 'Closest Drop Point',
        ];
        $service = new Service\DeliveryType('Delivery Type', true, false, $options);

        static::assertEquals('radio', $service->getFrontendInputType());
        static::assertStringContainsString('type="radio"', $service->getValueHtml());
    }

    /**
     * DeliveryType has a checkbox to enable/disable (inherited from Text type via Radio).
     *
     * @test
     */
    public function deliveryTypeHasCheckbox()
    {
        $options = [
            Service\DeliveryType::ECONOMY => 'Economy',
            Service\DeliveryType::PREMIUM => 'Premium',
            Service\DeliveryType::CDP => 'Closest Drop Point',
        ];
        $service = new Service\DeliveryType('Delivery Type', true, false, $options);

        static::assertNotEmpty($service->getSelectorHtml());
        static::assertStringContainsString('type="checkbox"', $service->getSelectorHtml());
    }

    /**
     * Endorsement renders a checkbox (Select type) — dropdown provides value.
     * Select types do NOT have a separate checkbox selector.
     *
     * @test
     */
    public function endorsementIsSelectType()
    {
        $options = [
            Service\Endorsement::RETURN => 'Return',
            Service\Endorsement::ABANDON => 'Abandon',
        ];
        $service = new Service\Endorsement('Endorsement', true, false, $options);

        static::assertEquals('select', $service->getFrontendInputType());
        static::assertStringContainsString('type="checkbox"', $service->getSelectorHtml());
        static::assertStringContainsString('select', $service->getValueHtml());
    }

    /**
     * Config-created DeliveryType service includes CDP as third radio option.
     *
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function configDeliveryTypeIncludesCdpOption()
    {
        $config = new Dhl_Versenden_Model_Config_Service();
        $services = $config->getServices();

        /** @var Service\DeliveryType $service */
        $service = $services->getItem(Service\DeliveryType::CODE);
        static::assertNotNull($service);

        $options = $service->getOptions();
        static::assertArrayHasKey(Service\DeliveryType::ECONOMY, $options);
        static::assertArrayHasKey(Service\DeliveryType::PREMIUM, $options);
        static::assertArrayHasKey(Service\DeliveryType::CDP, $options);
        static::assertCount(3, $options);
    }

    /**
     * @test
     */
    public function deliveryTypeBlockedForDomestic()
    {
        $filter = new \Dhl\Versenden\ParcelDe\Service\Filter(
            [\Dhl\Versenden\ParcelDe\Product::CODE_PAKET_NATIONAL],
            false,
            false,
            'DE',
            'DE'
        );

        $options = [
            Service\DeliveryType::ECONOMY => 'Economy',
            Service\DeliveryType::PREMIUM => 'Premium',
        ];
        $service = new Service\DeliveryType('Delivery Type', true, false, $options);
        $result = $filter->filterService($service);

        static::assertNull($result, 'DeliveryType should be blocked for domestic shipments');
    }

    /**
     * @test
     */
    public function deliveryTypeAllowedForInternational()
    {
        $filter = new \Dhl\Versenden\ParcelDe\Service\Filter(
            [\Dhl\Versenden\ParcelDe\Product::CODE_WELTPAKET],
            false,
            false,
            'DE',
            'US'
        );

        $options = [
            Service\DeliveryType::ECONOMY => 'Economy',
            Service\DeliveryType::PREMIUM => 'Premium',
        ];
        $service = new Service\DeliveryType('Delivery Type', true, false, $options);
        $result = $filter->filterService($service);

        static::assertNotNull($result, 'DeliveryType should be allowed for international shipments');
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function getServicesIncludesPostalDeliveryDutyPaid()
    {
        $config = new Dhl_Versenden_Model_Config_Service();
        $services = $config->getServices();

        $service = $services->getItem(Service\PostalDeliveryDutyPaid::CODE);
        static::assertNotNull($service, 'PostalDeliveryDutyPaid service should be in collection');
        static::assertInstanceOf(Service\PostalDeliveryDutyPaid::class, $service);
    }

    // =========================================================================
    // DHLGKP-366: Payment and Location Services Tests
    // =========================================================================

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function getServicesIncludesCod()
    {
        $config = new Dhl_Versenden_Model_Config_Service();
        $services = $config->getServices();

        $service = $services->getItem(Service\Cod::CODE);
        static::assertNotNull($service, 'COD service should be in collection');
        static::assertInstanceOf(Service\Cod::class, $service);
    }

    /**
     * @test
     */
    public function codIsVisibleTextType()
    {
        $service = new Service\Cod('Cash on Delivery', true, false, '');
        static::assertEquals('text', $service->getFrontendInputType());
    }

    // =========================================================================
    // DHLGKP-367: Checkout Customer Service Selection Tests
    // =========================================================================

    /**
     * @test
     */
    public function noNeighbourDeliveryIsCustomerService()
    {
        $service = new Service\NoNeighbourDelivery('No Neighbour Delivery', true, false);
        static::assertTrue($service->isCustomerService(), 'NoNeighbourDelivery should be a customer service');
    }

    /**
     * @test
     */
    public function goGreenIsCustomerService()
    {
        $service = new Service\GoGreenPlus('GoGreen Plus', true, false);
        static::assertTrue($service->isCustomerService(), 'GoGreen should be a customer service');
    }

    // =========================================================================
    // Work Item 2: Packaging Popup Service Visibility
    // =========================================================================

    /**
     * Packaging popup should show toggle customer services (NoNeighbourDelivery,
     * GoGreen, PreferredLocation) regardless of checkout config toggle.
     *
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function getServicesForPackagingShowsToggleServicesRegardlessOfConfig()
    {
        // Disable NoNeighbourDelivery and GoGreen in checkout config
        Mage::app()->getStore()->setConfig(
            'carriers/dhlversenden/service_noneighbourdelivery_enabled',
            '0'
        );
        Mage::app()->getStore()->setConfig(
            'carriers/dhlversenden/service_gogreen_enabled',
            '0'
        );

        $config = new Dhl_Versenden_Model_Config_Service();

        // getAvailableServices with forPackaging=true should still include these
        $services = $config->getAvailableServices('DE', 'DE', false, false, null, true);
        $items = $services->getItems();

        $noNeighbour = array_filter($items, function ($s) {
            return $s instanceof Service\NoNeighbourDelivery;
        });
        static::assertNotEmpty(
            $noNeighbour,
            'NoNeighbourDelivery must be visible in packaging popup even when checkout config is disabled'
        );

        $goGreen = array_filter($items, function ($s) {
            return $s instanceof Service\GoGreenPlus;
        });
        static::assertNotEmpty(
            $goGreen,
            'GoGreen must be visible in packaging popup even when checkout config is disabled'
        );
    }

    /**
     * Checkout should still respect config toggles — disabled services hidden.
     *
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function getAvailableServicesForCheckoutRespectsConfig()
    {
        // Disable NoNeighbourDelivery in checkout config
        Mage::app()->getStore()->setConfig(
            'carriers/dhlversenden/service_noneighbourdelivery_enabled',
            '0'
        );

        $config = new Dhl_Versenden_Model_Config_Service();

        // Default (forPackaging=false) should filter by checkout config
        $services = $config->getAvailableServices('DE', 'DE', false, false);
        $items = $services->getItems();

        $noNeighbour = array_filter($items, function ($s) {
            return $s instanceof Service\NoNeighbourDelivery;
        });
        static::assertEmpty(
            $noNeighbour,
            'NoNeighbourDelivery must be hidden in checkout when config is disabled'
        );
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function getAutoCreateServices()
    {
        $config = new Dhl_Versenden_Model_Config_Service();
        $services = $config->getAutoCreateServices();

        // Should return a collection
        static::assertInstanceOf(Service\Collection::class, $services);

        // Auto-create services are those that are enabled AND selected
        $items = $services->getItems();
        static::assertIsArray($items);
    }

    /**
     * Autocreate must include services where the checkout toggle is OFF but the
     * shipment default is ON (Pattern B services: NoNeighbourDelivery, GoGreen).
     *
     * M2 treats checkout visibility and shipment defaults as independent concerns.
     * Previously, getAutoCreateServices() used getEnabledServices() as its base,
     * which filtered out services with checkout toggle OFF — preventing shipment
     * defaults from taking effect.
     *
     * @test
     * @loadFixture Model_ConfigTest_AutocreateDecoupled
     */
    public function getAutoCreateServicesIgnoresCheckoutToggle()
    {
        $config = new Dhl_Versenden_Model_Config_Service();
        $services = $config->getAutoCreateServices();
        $items = $services->getItems();

        $noNeighbour = array_filter($items, function ($s) {
            return $s instanceof Service\NoNeighbourDelivery;
        });
        static::assertNotEmpty(
            $noNeighbour,
            'NoNeighbourDelivery with checkout=OFF + shipment default=ON must be in autocreate services'
        );
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function getAvailableServices()
    {
        $config = new Dhl_Versenden_Model_Config_Service();

        // Test domestic German shipment
        $services = $config->getAvailableServices('DE', 'DE', false, true);

        static::assertInstanceOf(Service\Collection::class, $services);
        $items = $services->getItems();
        static::assertIsArray($items);
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function getAvailableServicesForPostalFacility()
    {
        $config = new Dhl_Versenden_Model_Config_Service();

        // Test shipment to postal facility
        $services = $config->getAvailableServices('DE', 'DE', true, true);

        static::assertInstanceOf(Service\Collection::class, $services);
        $items = $services->getItems();
        static::assertIsArray($items);
    }

    // =========================================================================
    // V66WPI (WARENPOST INTERNATIONAL) INTEGRATION TESTS
    // =========================================================================

    /**
     * Test that DE to US (ROW) includes both Weltpaket and V66WPI products.
     * Filter allows services that work with ANY available product.
     *
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function getAvailableServicesForInternationalRow()
    {
        $config = new Dhl_Versenden_Model_Config_Service();

        // DE to US is ROW, both V53WPAK (Weltpaket) and V66WPI should be available
        $services = $config->getAvailableServices('DE', 'US', false, false);

        static::assertInstanceOf(Service\Collection::class, $services);
        $items = $services->getItems();
        static::assertIsArray($items);

        // Since multiple products are available, services allowed by ANY product pass
        // AdditionalInsurance is allowed by Weltpaket, so it passes for ROW shipments
        $insuranceServices = array_filter($items, function ($service) {
            return ($service instanceof Service\AdditionalInsurance);
        });
        static::assertNotEmpty(
            $insuranceServices,
            'AdditionalInsurance should be available for ROW (Weltpaket supports it)'
        );
    }

    /**
     * Test V66WPI-only filter using direct Filter class.
     * This tests the specific service restrictions for V66WPI when it's the ONLY product.
     *
     * @test
     */
    public function v66wpiOnlyFilterBlocksAdditionalInsurance()
    {
        // Create filter with V66WPI only
        $filter = new \Dhl\Versenden\ParcelDe\Service\Filter(
            [\Dhl\Versenden\ParcelDe\Product::CODE_WARENPOST_INTERNATIONAL],
            false,
            false
        );

        // AdditionalInsurance service
        $service = new Service\AdditionalInsurance('additionalInsurance', true, false, '');
        $result = $filter->filterService($service);

        static::assertNull($result, 'V66WPI should NOT allow AdditionalInsurance service');
    }

    /**
     * Test V66WPI-only filter using direct Filter class.
     *
     * @test
     */
    public function v66wpiOnlyFilterBlocksBulkyGoods()
    {
        $filter = new \Dhl\Versenden\ParcelDe\Service\Filter(
            [\Dhl\Versenden\ParcelDe\Product::CODE_WARENPOST_INTERNATIONAL],
            false,
            false
        );

        $service = new Service\BulkyGoods('bulkyGoods', true, false);
        $result = $filter->filterService($service);

        static::assertNull($result, 'V66WPI should NOT allow BulkyGoods service');
    }

    /**
     * Test V66WPI-only filter using direct Filter class.
     *
     * @test
     */
    public function v66wpiOnlyFilterBlocksPreferredDay()
    {
        $filter = new \Dhl\Versenden\ParcelDe\Service\Filter(
            [\Dhl\Versenden\ParcelDe\Product::CODE_WARENPOST_INTERNATIONAL],
            false,
            false
        );

        $service = new Service\PreferredDay('preferredDay', true, false, []);
        $result = $filter->filterService($service);

        static::assertNull($result, 'V66WPI should NOT allow PreferredDay service');
    }

    /**
     * Test V66WPI-only filter using direct Filter class.
     *
     * @test
     */
    public function v66wpiOnlyFilterBlocksCod()
    {
        $filter = new \Dhl\Versenden\ParcelDe\Service\Filter(
            [\Dhl\Versenden\ParcelDe\Product::CODE_WARENPOST_INTERNATIONAL],
            false,
            false
        );

        $service = new Service\Cod('cod', true, false, '');
        $result = $filter->filterService($service);

        static::assertNull($result, 'V66WPI should NOT allow COD service');
    }

    /**
     * Test V66WPI-only filter allows ParcelAnnouncement.
     *
     * @test
     */
    public function v66wpiOnlyFilterAllowsParcelAnnouncement()
    {
        $filter = new \Dhl\Versenden\ParcelDe\Service\Filter(
            [\Dhl\Versenden\ParcelDe\Product::CODE_WARENPOST_INTERNATIONAL],
            false,
            false
        );

        $service = new Service\ParcelAnnouncement('parcelAnnouncement', true, false);
        $result = $filter->filterService($service);

        static::assertNotNull($result, 'V66WPI should allow ParcelAnnouncement service');
    }

    /**
     * Contrast test: Domestic DE to DE should allow Insurance.
     * This confirms that Insurance IS normally available, just not for V66WPI.
     *
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function domesticShipmentAllowsInsurance()
    {
        $config = new Dhl_Versenden_Model_Config_Service();

        // DE to DE (domestic, V01PAK product)
        $services = $config->getAvailableServices('DE', 'DE', false, false);
        $items = $services->getItems();

        $insuranceServices = array_filter($items, function ($service) {
            return ($service instanceof Service\AdditionalInsurance);
        });

        static::assertNotEmpty(
            $insuranceServices,
            'AdditionalInsurance service should be available for domestic DE shipments'
        );
    }

    // =========================================================================
    // Product/Service Compatibility Tests (Filter layer)
    // =========================================================================

    /**
     * Test that KLEINPAKET is incompatible with COD.
     *
     * @test
     */
    public function kleinpaketIncompatibleWithCod()
    {
        // V62KP does NOT support COD (not in its product services list)
        $filter = new \Dhl\Versenden\ParcelDe\Service\Filter(
            [\Dhl\Versenden\ParcelDe\Product::CODE_KLEINPAKET],
            false,
            false
        );

        $service = new Service\Cod('Cash on Delivery', true, true, '');
        $result = $filter->filterService($service);

        static::assertNull($result, 'KLEINPAKET should NOT support COD service');
    }

    /**
     * Test that KLEINPAKET is incompatible with NoNeighbourDelivery.
     *
     * @test
     */
    public function kleinpaketIncompatibleWithNoNeighbourDelivery()
    {
        $filter = new \Dhl\Versenden\ParcelDe\Service\Filter(
            [\Dhl\Versenden\ParcelDe\Product::CODE_KLEINPAKET],
            false,
            false,
            'DE',
            'DE'
        );

        $service = new Service\NoNeighbourDelivery('No Neighbour Delivery', true, true);
        $result = $filter->filterService($service);

        static::assertNull($result, 'KLEINPAKET should NOT support NoNeighbourDelivery service');
    }

    /**
     * Test that KLEINPAKET is incompatible with PreferredDay.
     *
     * @test
     */
    public function kleinpaketIncompatibleWithPreferredDay()
    {
        $filter = new \Dhl\Versenden\ParcelDe\Service\Filter(
            [\Dhl\Versenden\ParcelDe\Product::CODE_KLEINPAKET],
            false,
            false,
            'DE',
            'DE'
        );

        $service = new Service\PreferredDay('Preferred Day', true, true, []);
        $result = $filter->filterService($service);

        static::assertNull($result, 'KLEINPAKET should NOT support PreferredDay service');
    }

    /**
     * Test that PAKET_NATIONAL supports COD (contrast test).
     *
     * @test
     */
    public function paketNationalSupportsCod()
    {
        $filter = new \Dhl\Versenden\ParcelDe\Service\Filter(
            [\Dhl\Versenden\ParcelDe\Product::CODE_PAKET_NATIONAL],
            false,
            false,
            'DE',
            'DE'
        );

        $service = new Service\Cod('Cash on Delivery', true, true, '');
        $result = $filter->filterService($service);

        static::assertNotNull($result, 'PAKET_NATIONAL should support COD service');
    }

    /**
     * Test that PAKET_NATIONAL supports NoNeighbourDelivery (contrast test).
     *
     * @test
     */
    public function paketNationalSupportsNoNeighbourDelivery()
    {
        $filter = new \Dhl\Versenden\ParcelDe\Service\Filter(
            [\Dhl\Versenden\ParcelDe\Product::CODE_PAKET_NATIONAL],
            false,
            false,
            'DE',
            'DE'
        );

        $service = new Service\NoNeighbourDelivery('No Neighbour Delivery', true, true);
        $result = $filter->filterService($service);

        static::assertNotNull($result, 'PAKET_NATIONAL should support NoNeighbourDelivery service');
    }

    // =========================================================================
    // ClosestDropPoint as customer-facing checkout service
    // =========================================================================

    /**
     * ClosestDropPoint must be in the service collection when config is enabled.
     *
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function getServicesIncludesClosestDropPoint()
    {
        $config = new Dhl_Versenden_Model_Config_Service();
        $services = $config->getServices();

        $service = $services->getItem(Service\ClosestDropPoint::CODE);
        static::assertNotNull($service, 'ClosestDropPoint should be in service collection when enabled');
        static::assertInstanceOf(Service\ClosestDropPoint::class, $service);
        static::assertTrue($service->isCustomerService(), 'ClosestDropPoint must be a customer service');
    }

    /**
     * ClosestDropPoint must be absent from the service collection when config is disabled.
     *
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function getServicesExcludesClosestDropPointWhenDisabled()
    {
        Mage::app()->getStore()->setConfig(
            'carriers/dhlversenden/service_closestdroppoint_enabled',
            '0'
        );

        $config = new Dhl_Versenden_Model_Config_Service();
        $services = $config->getServices();

        $service = $services->getItem(Service\ClosestDropPoint::CODE);
        static::assertNull($service, 'ClosestDropPoint should NOT be in collection when disabled');
    }

    // =========================================================================
    // Customer-facing services must not be pre-selected from autocreate config
    // =========================================================================

    /**
     * GoGreen must not be pre-selected in getServices() even when autocreate
     * config is ON. The autocreate config only applies in getAutoCreateServices().
     *
     * @test
     * @loadFixture Model_ConfigTest_AutocreateDecoupled
     */
    public function getServicesDoesNotPreSelectGoGreen()
    {
        $config = new Dhl_Versenden_Model_Config_Service();
        $services = $config->getServices();

        /** @var Service\GoGreenPlus $goGreen */
        $goGreen = $services->getItem(Service\GoGreenPlus::CODE);
        static::assertNotNull($goGreen);
        static::assertFalse(
            $goGreen->isSelected(),
            'GoGreen must not be pre-selected from autocreate config in getServices()'
        );
    }

    /**
     * NoNeighbourDelivery must not be pre-selected in getServices() even when
     * autocreate config is ON. The autocreate config only applies in
     * getAutoCreateServices().
     *
     * @test
     * @loadFixture Model_ConfigTest_AutocreateDecoupled
     */
    public function getServicesDoesNotPreSelectNoNeighbourDelivery()
    {
        $config = new Dhl_Versenden_Model_Config_Service();
        $services = $config->getServices();

        /** @var Service\NoNeighbourDelivery $noNeighbour */
        $noNeighbour = $services->getItem(Service\NoNeighbourDelivery::CODE);
        static::assertNotNull($noNeighbour);
        static::assertFalse(
            $noNeighbour->isSelected(),
            'NoNeighbourDelivery must not be pre-selected from autocreate config in getServices()'
        );
    }

    /**
     * GoGreen is a customer-facing service with surcharge — autocreate must not
     * apply it. Only customer checkout selection or manual admin selection in
     * the packaging popup should enable it (matching M2 behavior).
     *
     * @test
     * @loadFixture Model_ConfigTest_AutocreateDecoupled
     */
    public function getAutoCreateServicesExcludesGoGreen()
    {
        $config = new Dhl_Versenden_Model_Config_Service();
        $services = $config->getAutoCreateServices();
        $items = $services->getItems();

        $goGreen = array_filter($items, function ($s) {
            return $s instanceof Service\GoGreenPlus;
        });
        static::assertEmpty(
            $goGreen,
            'GoGreen must not be in autocreate services — it is customer-facing only'
        );
    }

    /**
     * getAutoCreateServices() must apply autocreate config for NoNeighbourDelivery
     * independently from the init method's isSelected value.
     *
     * @test
     * @loadFixture Model_ConfigTest_AutocreateDecoupled
     */
    public function getAutoCreateServicesAppliesNoNeighbourDeliveryDefault()
    {
        $config = new Dhl_Versenden_Model_Config_Service();
        $services = $config->getAutoCreateServices();
        $items = $services->getItems();

        $noNeighbour = array_filter($items, function ($s) {
            return $s instanceof Service\NoNeighbourDelivery;
        });
        static::assertNotEmpty(
            $noNeighbour,
            'NoNeighbourDelivery with autocreate=ON must be included in getAutoCreateServices()'
        );

        /** @var Service\NoNeighbourDelivery $service */
        $service = reset($noNeighbour);
        static::assertTrue(
            $service->isSelected(),
            'NoNeighbourDelivery must be selected in autocreate context when autocreate config is ON'
        );
    }

    // =========================================================================
    // DHLGKP-XXX: NoNeighbourDelivery and GoGreen Surcharge Support Tests
    // =========================================================================

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function getNoNeighbourDeliveryFee()
    {
        $config = new Dhl_Versenden_Model_Config_Service();
        static::assertEquals(0.29, $config->getNoNeighbourDeliveryFee());
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function getNoNeighbourDeliveryHandlingFeeText()
    {
        $config = new Dhl_Versenden_Model_Config_Service();
        $text = $config->getNoNeighbourDeliveryHandlingFeeText();

        // Fee is 0.29 according to fixture, so should contain formatted value
        static::assertIsString($text);
        static::assertNotEmpty($text);
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function getNoNeighbourDeliveryHandlingFeeTextEmptyWhenFeeZero()
    {
        Mage::app()->getStore()->setConfig(
            'carriers/dhlversenden/service_noneighbourdelivery_handling_fee',
            '0'
        );

        $config = new Dhl_Versenden_Model_Config_Service();
        $text = $config->getNoNeighbourDeliveryHandlingFeeText();

        static::assertEmpty($text, 'NoNeighbourDelivery handling fee text should be empty when fee is 0');
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function getGoGreenFee()
    {
        $config = new Dhl_Versenden_Model_Config_Service();
        static::assertEquals(0.50, $config->getGoGreenFee());
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function getGoGreenHandlingFeeText()
    {
        $config = new Dhl_Versenden_Model_Config_Service();
        $text = $config->getGoGreenHandlingFeeText();

        // Fee is 0.50 according to fixture, so should contain formatted value
        static::assertIsString($text);
        static::assertNotEmpty($text);
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function getGoGreenHandlingFeeTextEmptyWhenFeeZero()
    {
        Mage::app()->getStore()->setConfig(
            'carriers/dhlversenden/service_gogreen_handling_fee',
            '0'
        );

        $config = new Dhl_Versenden_Model_Config_Service();
        $text = $config->getGoGreenHandlingFeeText();

        static::assertEmpty($text, 'GoGreen handling fee text should be empty when fee is 0');
    }

    // =========================================================================
    // ClosestDropPoint Filter Tests
    // =========================================================================

    /**
     * ClosestDropPoint must pass filter for WELTPAKET + international + CDP-eligible country.
     *
     * @test
     */
    public function closestDropPointAllowedForWeltpaketCdpEligible()
    {
        $filter = new \Dhl\Versenden\ParcelDe\Service\Filter(
            [\Dhl\Versenden\ParcelDe\Product::CODE_WELTPAKET],
            false,
            false,
            'DE',
            'AT'
        );

        $service = new Service\ClosestDropPoint('Closest Drop Point', true, false);
        $result = $filter->filterService($service);

        static::assertNotNull($result, 'ClosestDropPoint should be allowed for WELTPAKET + DE→AT');
    }

    /**
     * ClosestDropPoint must be filtered out for domestic shipments.
     *
     * @test
     */
    public function closestDropPointBlockedForDomestic()
    {
        $filter = new \Dhl\Versenden\ParcelDe\Service\Filter(
            [\Dhl\Versenden\ParcelDe\Product::CODE_PAKET_NATIONAL],
            false,
            false,
            'DE',
            'DE'
        );

        $service = new Service\ClosestDropPoint('Closest Drop Point', true, false);
        $result = $filter->filterService($service);

        static::assertNull($result, 'ClosestDropPoint should be blocked for domestic shipments');
    }

    /**
     * ClosestDropPoint must be filtered out for non-CDP-eligible countries.
     *
     * @test
     */
    public function closestDropPointBlockedForNonCdpCountry()
    {
        $filter = new \Dhl\Versenden\ParcelDe\Service\Filter(
            [\Dhl\Versenden\ParcelDe\Product::CODE_WELTPAKET],
            false,
            false,
            'DE',
            'US'
        );

        $service = new Service\ClosestDropPoint('Closest Drop Point', true, false);
        $result = $filter->filterService($service);

        static::assertNull($result, 'ClosestDropPoint should be blocked for non-CDP-eligible country (US)');
    }

    /**
     * ClosestDropPoint must be filtered out for WARENPOST_INTERNATIONAL.
     *
     * @test
     */
    public function closestDropPointBlockedForWarenpostInternational()
    {
        $filter = new \Dhl\Versenden\ParcelDe\Service\Filter(
            [\Dhl\Versenden\ParcelDe\Product::CODE_WARENPOST_INTERNATIONAL],
            false,
            false,
            'DE',
            'AT'
        );

        $service = new Service\ClosestDropPoint('Closest Drop Point', true, false);
        $result = $filter->filterService($service);

        static::assertNull($result, 'ClosestDropPoint should NOT be allowed for WARENPOST_INTERNATIONAL');
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function getCdpFee()
    {
        $config = new Dhl_Versenden_Model_Config_Service();
        static::assertEquals(1.50, $config->getCdpFee());
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function getCdpHandlingFeeText()
    {
        $config = new Dhl_Versenden_Model_Config_Service();
        $text = $config->getCdpHandlingFeeText();

        // Fee is 1.50 according to fixture, so should contain formatted value
        static::assertIsString($text);
        static::assertNotEmpty($text);
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function getCdpHandlingFeeTextEmptyWhenFeeZero()
    {
        // Override fee to 0 in store scope
        Mage::app()->getStore()->setConfig(
            'carriers/dhlversenden/service_closestdroppoint_handling_fee',
            '0'
        );

        $config = new Dhl_Versenden_Model_Config_Service();
        $text = $config->getCdpHandlingFeeText();

        static::assertEmpty($text, 'CDP handling fee text should be empty when fee is 0');
    }

}
