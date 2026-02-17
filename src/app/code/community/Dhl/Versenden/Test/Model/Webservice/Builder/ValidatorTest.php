<?php

/**
 * See LICENSE.md for license details.
 */

use Dhl\Versenden\ParcelDe\Config\ValidationException;
use Dhl\Versenden\ParcelDe\Product;
use Dhl\Versenden\ParcelDe\Service;

/**
 * Validator Test - Fixture-Based Integration Testing
 *
 * Tests business rule validation using real Magento models and fixtures.
 * This approach validates the complete integration stack including:
 * - COD detection from payment method configuration
 * - Partial shipment detection from order quantities
 * - Service selection validation
 * - Product-service compatibility rules
 *
 * Architecture Note:
 * - Uses EcomDev_PHPUnit fixture pattern (not mocks)
 * - Tests real Magento config behavior
 * - Validates ServiceBuilder integration
 * - Follows existing Builder test patterns
 *
 * @see Dhl_Versenden_Model_Webservice_Builder_Validator
 */
class Dhl_Versenden_Test_Model_Webservice_Builder_ValidatorTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @var Dhl_Versenden_Model_Webservice_Builder_Validator
     */
    protected $_validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->_validator = new Dhl_Versenden_Model_Webservice_Builder_Validator();
    }

    /**
     * Create shipment request from real Magento models
     *
     * @param Mage_Sales_Model_Order_Shipment $shipment
     * @param array $serviceSelection
     * @param string $productCode
     * @return Mage_Shipping_Model_Shipment_Request
     */
    protected function _createRequest(
        Mage_Sales_Model_Order_Shipment $shipment,
        array $serviceSelection = [],
        $productCode = Product::CODE_PAKET_NATIONAL
    ) {
        $request = new Mage_Shipping_Model_Shipment_Request();
        $request->setOrderShipment($shipment);
        $request->setData('gk_api_product', $productCode);
        $request->setData('services', [
            'shipment_service' => $serviceSelection,
            'service_setting' => [],
        ]);

        return $request;
    }

    // =========================================================================
    // PARTIAL SHIPMENT VALIDATION TESTS
    // =========================================================================

    /**
     * @test
     * @loadFixture Model_Webservice_Builder_ValidatorTest_orders
     * @loadFixture Model_Webservice_Builder_ValidatorTest_shipments
     * @loadFixture Model_Webservice_Builder_ValidatorTest_config
     */
    public function fullShipmentWithCodPasses()
    {
        $shipment = Mage::getModel('sales/order_shipment')->load(1);
        $request = $this->_createRequest($shipment);

        // Full shipment (5 of 5) with COD should pass
        $this->_validator->validate($request);
        $this->addToAssertionCount(1);
    }

    /**
     * @test
     * @loadFixture Model_Webservice_Builder_ValidatorTest_orders
     * @loadFixture Model_Webservice_Builder_ValidatorTest_shipments
     * @loadFixture Model_Webservice_Builder_ValidatorTest_config
     */
    public function partialShipmentWithCodFails()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Cannot do partial shipment with COD or Additional Insurance');

        $shipment = Mage::getModel('sales/order_shipment')->load(2);
        $request = $this->_createRequest($shipment);

        // Partial shipment (2 of 5) with COD should fail
        $this->_validator->validate($request);
    }

    /**
     * @test
     * @loadFixture Model_Webservice_Builder_ValidatorTest_orders
     * @loadFixture Model_Webservice_Builder_ValidatorTest_shipments
     * @loadFixture Model_Webservice_Builder_ValidatorTest_config
     */
    public function partialShipmentWithInsuranceFails()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Cannot do partial shipment with COD or Additional Insurance');

        $shipment = Mage::getModel('sales/order_shipment')->load(3);
        $request = $this->_createRequest($shipment, [
            Service\AdditionalInsurance::CODE => '1',
        ]);

        // Partial shipment (2 of 5) with Insurance should fail
        $this->_validator->validate($request);
    }

    /**
     * @test
     * @loadFixture Model_Webservice_Builder_ValidatorTest_orders
     * @loadFixture Model_Webservice_Builder_ValidatorTest_shipments
     * @loadFixture Model_Webservice_Builder_ValidatorTest_config
     */
    public function partialShipmentWithCodAndInsuranceFails()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Cannot do partial shipment with COD or Additional Insurance');

        $shipment = Mage::getModel('sales/order_shipment')->load(2);
        $request = $this->_createRequest($shipment, [
            Service\AdditionalInsurance::CODE => '1',
        ]);

        // Partial shipment (2 of 5) with both COD and Insurance should fail
        $this->_validator->validate($request);
    }

    /**
     * @test
     * @loadFixture Model_Webservice_Builder_ValidatorTest_orders
     * @loadFixture Model_Webservice_Builder_ValidatorTest_shipments
     * @loadFixture Model_Webservice_Builder_ValidatorTest_config
     */
    public function partialShipmentWithoutCodOrInsurancePasses()
    {
        $shipment = Mage::getModel('sales/order_shipment')->load(4);
        $request = $this->_createRequest($shipment);

        // Partial shipment (2 of 5) without COD or Insurance should pass
        $this->_validator->validate($request);
        $this->addToAssertionCount(1);
    }

    // =========================================================================
    // KLEINPAKET SERVICE RESTRICTION TESTS
    // =========================================================================

    /**
     * @test
     * @loadFixture Model_Webservice_Builder_ValidatorTest_orders
     * @loadFixture Model_Webservice_Builder_ValidatorTest_shipments
     * @loadFixture Model_Webservice_Builder_ValidatorTest_config
     */
    public function kleinpaketWithoutServicesPasses()
    {
        $shipment = Mage::getModel('sales/order_shipment')->load(4);
        $request = $this->_createRequest($shipment, [], Product::CODE_KLEINPAKET);

        // Kleinpaket without services should pass
        $this->_validator->validate($request);
        $this->addToAssertionCount(1);
    }

    /**
     * @test
     * @loadFixture Model_Webservice_Builder_ValidatorTest_orders
     * @loadFixture Model_Webservice_Builder_ValidatorTest_shipments
     * @loadFixture Model_Webservice_Builder_ValidatorTest_config
     */
    public function kleinpaketWithInsuranceFails()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Kleinpaket cannot be booked with the services');

        $shipment = Mage::getModel('sales/order_shipment')->load(1);
        $request = $this->_createRequest($shipment, [
            Service\AdditionalInsurance::CODE => '1',
        ], Product::CODE_KLEINPAKET);

        // Kleinpaket with Insurance should fail
        $this->_validator->validate($request);
    }

    /**
     * @test
     * @loadFixture Model_Webservice_Builder_ValidatorTest_orders
     * @loadFixture Model_Webservice_Builder_ValidatorTest_shipments
     * @loadFixture Model_Webservice_Builder_ValidatorTest_config
     */
    public function kleinpaketWithBulkyGoodsFails()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Kleinpaket cannot be booked with the services');

        $shipment = Mage::getModel('sales/order_shipment')->load(1);
        $request = $this->_createRequest($shipment, [
            Service\BulkyGoods::CODE => '1',
        ], Product::CODE_KLEINPAKET);

        // Kleinpaket with BulkyGoods should fail
        $this->_validator->validate($request);
    }

    /**
     * @test
     * @loadFixture Model_Webservice_Builder_ValidatorTest_orders
     * @loadFixture Model_Webservice_Builder_ValidatorTest_shipments
     * @loadFixture Model_Webservice_Builder_ValidatorTest_config
     */
    public function kleinpaketWithCodFails()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Kleinpaket cannot be booked with the services');

        $shipment = Mage::getModel('sales/order_shipment')->load(1);
        $request = $this->_createRequest($shipment, [], Product::CODE_KLEINPAKET);

        // Kleinpaket with COD payment method should fail
        $this->_validator->validate($request);
    }

    /**
     * @test
     * @loadFixture Model_Webservice_Builder_ValidatorTest_orders
     * @loadFixture Model_Webservice_Builder_ValidatorTest_shipments
     * @loadFixture Model_Webservice_Builder_ValidatorTest_config
     */
    public function kleinpaketWithPreferredDayFails()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Kleinpaket cannot be booked with the services');

        $shipment = Mage::getModel('sales/order_shipment')->load(1);
        $request = $this->_createRequest($shipment, [
            Service\PreferredDay::CODE => '1',
        ], Product::CODE_KLEINPAKET);

        // Kleinpaket with PreferredDay should fail
        $this->_validator->validate($request);
    }

    /**
     * @test
     * @loadFixture Model_Webservice_Builder_ValidatorTest_orders
     * @loadFixture Model_Webservice_Builder_ValidatorTest_shipments
     * @loadFixture Model_Webservice_Builder_ValidatorTest_config
     */
    public function kleinpaketWithVisualCheckOfAgeFails()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Kleinpaket cannot be booked with the services');

        $shipment = Mage::getModel('sales/order_shipment')->load(1);
        $request = $this->_createRequest($shipment, [
            Service\VisualCheckOfAge::CODE => '1',
        ], Product::CODE_KLEINPAKET);

        // Kleinpaket with VisualCheckOfAge should fail
        $this->_validator->validate($request);
    }

    /**
     * @test
     * @loadFixture Model_Webservice_Builder_ValidatorTest_orders
     * @loadFixture Model_Webservice_Builder_ValidatorTest_shipments
     * @loadFixture Model_Webservice_Builder_ValidatorTest_config
     */
    public function kleinpaketWithMultipleServicesFails()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Kleinpaket cannot be booked with the services');

        $shipment = Mage::getModel('sales/order_shipment')->load(1);
        $request = $this->_createRequest($shipment, [
            Service\AdditionalInsurance::CODE => '1',
            Service\BulkyGoods::CODE => '1',
        ], Product::CODE_KLEINPAKET);

        // Kleinpaket with multiple premium services should fail
        $this->_validator->validate($request);
    }

    // =========================================================================
    // VALID COMBINATION TESTS
    // =========================================================================

    /**
     * @test
     * @loadFixture Model_Webservice_Builder_ValidatorTest_orders
     * @loadFixture Model_Webservice_Builder_ValidatorTest_shipments
     * @loadFixture Model_Webservice_Builder_ValidatorTest_config
     */
    public function regularPaketWithAllServicesPasses()
    {
        $shipment = Mage::getModel('sales/order_shipment')->load(1);
        $request = $this->_createRequest($shipment, [
            Service\AdditionalInsurance::CODE => '1',
            Service\BulkyGoods::CODE => '1',
            Service\PreferredDay::CODE => '1',
            Service\VisualCheckOfAge::CODE => '1',
        ]);

        // Regular Paket with all services and COD should pass
        $this->_validator->validate($request);
        $this->addToAssertionCount(1);
    }

    // =========================================================================
    // V66WPI (WARENPOST INTERNATIONAL) VALIDATION TESTS
    // =========================================================================

    /**
     * V66WPI should pass validation without special restrictions.
     * Note: Service filtering for V66WPI is handled by Filter class, not Validator.
     *
     * @test
     * @loadFixture Model_Webservice_Builder_ValidatorTest_orders
     * @loadFixture Model_Webservice_Builder_ValidatorTest_shipments
     * @loadFixture Model_Webservice_Builder_ValidatorTest_config
     */
    public function warenpostInternationalWithoutServicesPasses()
    {
        $shipment = Mage::getModel('sales/order_shipment')->load(4);
        $request = $this->_createRequest($shipment, [], Product::CODE_WARENPOST_INTERNATIONAL);

        // V66WPI without services should pass validation
        $this->_validator->validate($request);
        $this->addToAssertionCount(1);
    }

    /**
     * V66WPI full shipment should pass validation.
     * Service restrictions are handled by Filter, not Validator.
     *
     * @test
     * @loadFixture Model_Webservice_Builder_ValidatorTest_orders
     * @loadFixture Model_Webservice_Builder_ValidatorTest_shipments
     * @loadFixture Model_Webservice_Builder_ValidatorTest_config
     */
    public function warenpostInternationalFullShipmentPasses()
    {
        $shipment = Mage::getModel('sales/order_shipment')->load(1);
        $request = $this->_createRequest($shipment, [], Product::CODE_WARENPOST_INTERNATIONAL);

        // V66WPI full shipment should pass (even with COD payment, Validator doesn't block)
        // Note: COD service itself is filtered by Filter class for V66WPI
        $this->_validator->validate($request);
        $this->addToAssertionCount(1);
    }

    /**
     * V66WPI partial shipment without COD/Insurance should pass.
     *
     * @test
     * @loadFixture Model_Webservice_Builder_ValidatorTest_orders
     * @loadFixture Model_Webservice_Builder_ValidatorTest_shipments
     * @loadFixture Model_Webservice_Builder_ValidatorTest_config
     */
    public function warenpostInternationalPartialShipmentWithoutCodPasses()
    {
        $shipment = Mage::getModel('sales/order_shipment')->load(4);
        $request = $this->_createRequest($shipment, [], Product::CODE_WARENPOST_INTERNATIONAL);

        // V66WPI partial shipment without COD/Insurance passes
        $this->_validator->validate($request);
        $this->addToAssertionCount(1);
    }

    /**
     * V66WPI partial shipment with COD fails (same rule as other products).
     *
     * @test
     * @loadFixture Model_Webservice_Builder_ValidatorTest_orders
     * @loadFixture Model_Webservice_Builder_ValidatorTest_shipments
     * @loadFixture Model_Webservice_Builder_ValidatorTest_config
     */
    public function warenpostInternationalPartialShipmentWithCodFails()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Cannot do partial shipment with COD or Additional Insurance');

        $shipment = Mage::getModel('sales/order_shipment')->load(2);
        $request = $this->_createRequest($shipment, [], Product::CODE_WARENPOST_INTERNATIONAL);

        // V66WPI partial shipment with COD fails (general rule applies)
        $this->_validator->validate($request);
    }
}
