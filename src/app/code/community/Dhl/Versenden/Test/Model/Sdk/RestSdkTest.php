<?php

/**
 * See LICENSE.md for license details.
 */

use Dhl\Sdk\ParcelDe\Shipping\Api\ServiceFactoryInterface;
use Dhl\Sdk\ParcelDe\Shipping\Api\ShipmentServiceInterface;
use Dhl\Sdk\ParcelDe\Shipping\Api\ShipmentOrderRequestBuilderInterface;

/**
 * Test REST SDK Integration
 *
 * Verifies that DHL REST SDK classes are accessible and autoloadable.
 * This test validates the acceptance criterion: "REST SDK classes accessible in module"
 *
 * @category  Dhl
 * @package   Dhl_Versenden
 */
class Dhl_Versenden_Test_Model_Sdk_RestSdkTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Test that REST SDK service factory interface exists and is autoloadable
     */
    public function testServiceFactoryInterfaceExists()
    {
        static::assertTrue(
            interface_exists(ServiceFactoryInterface::class),
            'ServiceFactoryInterface from REST SDK must be accessible',
        );
    }

    /**
     * Test that REST SDK shipment service interface exists and is autoloadable
     */
    public function testShipmentServiceInterfaceExists()
    {
        static::assertTrue(
            interface_exists(ShipmentServiceInterface::class),
            'ShipmentServiceInterface from REST SDK must be accessible',
        );
    }

    /**
     * Test that REST SDK request builder interface exists and is autoloadable
     */
    public function testRequestBuilderInterfaceExists()
    {
        static::assertTrue(
            interface_exists(ShipmentOrderRequestBuilderInterface::class),
            'ShipmentOrderRequestBuilderInterface from REST SDK must be accessible',
        );
    }

    /**
     * Test that REST SDK core classes can be instantiated
     */
    public function testServiceFactoryCanBeInstantiated()
    {
        $className = 'Dhl\Sdk\ParcelDe\Shipping\Service\ServiceFactory';

        static::assertTrue(
            class_exists($className),
            'ServiceFactory class from REST SDK must be accessible',
        );

        $factory = new $className();

        static::assertInstanceOf(
            ServiceFactoryInterface::class,
            $factory,
            'ServiceFactory must implement ServiceFactoryInterface',
        );
    }

    /**
     * Test that REST SDK request builder can be instantiated
     */
    public function testRequestBuilderCanBeInstantiated()
    {
        $className = 'Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder';

        static::assertTrue(
            class_exists($className),
            'ShipmentOrderRequestBuilder class from REST SDK must be accessible',
        );

        $builder = new $className();

        static::assertInstanceOf(
            ShipmentOrderRequestBuilderInterface::class,
            $builder,
            'ShipmentOrderRequestBuilder must implement ShipmentOrderRequestBuilderInterface',
        );
    }

    /**
     * Test that REST SDK authentication storage can be instantiated
     */
    public function testAuthenticationStorageCanBeInstantiated()
    {
        $className = 'Dhl\Sdk\ParcelDe\Shipping\Auth\AuthenticationStorage';

        static::assertTrue(
            class_exists($className),
            'AuthenticationStorage class from REST SDK must be accessible',
        );

        $auth = new $className(
            apiKey: 'test-api-key',
            user: 'test-user',
            password: 'test-password',
        );

        static::assertInstanceOf(
            'Dhl\Sdk\ParcelDe\Shipping\Api\Data\AuthenticationStorageInterface',
            $auth,
            'AuthenticationStorage must implement AuthenticationStorageInterface',
        );
    }
}
