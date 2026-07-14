<?php

/**
 * See LICENSE.md for license details.
 */

use Dhl\Versenden\ParcelDe\Service;

class Dhl_Versenden_Test_Model_Webservice_Builder_ServiceTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     */
    public function constructorArgShipperConfigMissing()
    {
        $this->expectException(Mage_Core_Exception::class);

        new Dhl_Versenden_Model_Webservice_Builder_Service([
            'shipment_config' => Mage::getModel('dhl_versenden/config_shipment'),
        ]);
    }

    /**
     * @test
     */
    public function constructorArgShipperConfigWrongType()
    {
        $this->expectException(Mage_Core_Exception::class);

        new Dhl_Versenden_Model_Webservice_Builder_Service([
            'shipper_config' => new stdClass(),
            'shipment_config' => Mage::getModel('dhl_versenden/config_shipment'),
        ]);
    }

    /**
     * @test
     */
    public function constructorArgShipmentConfigMissing()
    {
        $this->expectException(Mage_Core_Exception::class);

        new Dhl_Versenden_Model_Webservice_Builder_Service([
            'shipper_config' => Mage::getModel('dhl_versenden/config_shipper'),
        ]);
    }

    /**
     * @test
     */
    public function constructorArgShipmentConfigWrongType()
    {
        $this->expectException(Mage_Core_Exception::class);

        new Dhl_Versenden_Model_Webservice_Builder_Service([
            'shipper_config' => Mage::getModel('dhl_versenden/config_shipper'),
            'shipment_config' => new stdClass(),
        ]);
    }

    /**
     * @test
     */
    public function constructorArgServiceConfigMissing()
    {
        $this->expectException(Mage_Core_Exception::class);

        new Dhl_Versenden_Model_Webservice_Builder_Service([
            'shipper_config' => Mage::getModel('dhl_versenden/config_shipper'),
            'shipment_config' => Mage::getModel('dhl_versenden/config_shipment'),
        ]);
    }

    /**
     * @test
     */
    public function constructorArgServiceConfigWrongType()
    {
        $this->expectException(Mage_Core_Exception::class);

        new Dhl_Versenden_Model_Webservice_Builder_Service([
            'shipper_config' => Mage::getModel('dhl_versenden/config_shipper'),
            'shipment_config' => Mage::getModel('dhl_versenden/config_shipment'),
            'service_config' => new stdClass(),
        ]);
    }

    /**
     * @test
     * @loadFixture Model_ShipperConfigTest
     * @loadFixture Model_ShipmentConfigTest
     */
    public function buildServiceSelection()
    {
        // Create a mock SDK builder to verify method calls
        $sdkBuilder = $this->getMockBuilder(\Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $builder = new Dhl_Versenden_Model_Webservice_Builder_Service([
            'shipper_config' => Mage::getModel('dhl_versenden/config_shipper'),
            'shipment_config' => Mage::getModel('dhl_versenden/config_shipment'),
            'service_config' => Mage::getModel('dhl_versenden/config_service'),
        ]);

        $orderAmount        = '19.8600';
        $preferredNeighbour = 'Alf';
        $preferredLocation  = 'Melmac';

        $selectedServices = [
            Service\BulkyGoods::CODE => '1',
            Service\AdditionalInsurance::CODE => '1',
            Service\PreferredNeighbour::CODE => '1',
        ];

        $serviceDetails = [
            Service\PreferredNeighbour::CODE => $preferredNeighbour,
            Service\PreferredLocation::CODE => $preferredLocation,
        ];

        $serviceInfo = [
            'shipment_service' => $selectedServices,
            'service_setting' => $serviceDetails,
        ];

        $payment = new Mage_Sales_Model_Order_Payment();
        $payment->setMethod('cashondelivery');
        $order = new Mage_Sales_Model_Order();
        $order->setStoreId(1);
        $order->setBaseGrandTotal($orderAmount);
        $order->setPayment($payment);

        // Expect setInsuredValue to be called
        $sdkBuilder->expects(static::once())
            ->method('setInsuredValue')
            ->with(19.86);

        // Expect setCodAmount to be called
        $sdkBuilder->expects(static::once())
            ->method('setCodAmount')
            ->with(19.86);

        // Expect setBulkyGoods to be called
        $sdkBuilder->expects(static::once())
            ->method('setBulkyGoods');

        // Expect setPreferredNeighbour to be called
        $sdkBuilder->expects(static::once())
            ->method('setPreferredNeighbour')
            ->with($preferredNeighbour);

        // Call the new build method (returns void)
        $builder->build($sdkBuilder, $order, $serviceInfo);
    }

    /**
     * @test
     * @loadFixture Model_ShipperConfigTest
     * @loadFixture Model_ShipmentConfigTest
     */
    public function buildPreferredDayService()
    {
        $sdkBuilder = $this->getMockBuilder(\Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $builder = new Dhl_Versenden_Model_Webservice_Builder_Service([
            'shipper_config' => Mage::getModel('dhl_versenden/config_shipper'),
            'shipment_config' => Mage::getModel('dhl_versenden/config_shipment'),
            'service_config' => Mage::getModel('dhl_versenden/config_service'),
        ]);

        $preferredDate = '2024-12-25';
        $serviceInfo = [
            'shipment_service' => [
                Service\PreferredDay::CODE => '1',
            ],
            'service_setting' => [
                Service\PreferredDay::CODE => $preferredDate,
            ],
        ];

        $payment = new Mage_Sales_Model_Order_Payment();
        $payment->setMethod('checkmo');
        $order = new Mage_Sales_Model_Order();
        $order->setStoreId(1);
        $order->setBaseGrandTotal('25.00');
        $order->setPayment($payment);

        $sdkBuilder->expects(static::once())
            ->method('setPreferredDay')
            ->with($preferredDate);

        $builder->build($sdkBuilder, $order, $serviceInfo);
    }

    /**
     * @test
     * @loadFixture Model_ShipperConfigTest
     * @loadFixture Model_ShipmentConfigTest
     */
    public function buildPreferredLocationService()
    {
        $sdkBuilder = $this->getMockBuilder(\Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $builder = new Dhl_Versenden_Model_Webservice_Builder_Service([
            'shipper_config' => Mage::getModel('dhl_versenden/config_shipper'),
            'shipment_config' => Mage::getModel('dhl_versenden/config_shipment'),
            'service_config' => Mage::getModel('dhl_versenden/config_service'),
        ]);

        $preferredLocation = 'Garage';
        $serviceInfo = [
            'shipment_service' => [
                Service\PreferredLocation::CODE => '1',
            ],
            'service_setting' => [
                Service\PreferredLocation::CODE => $preferredLocation,
            ],
        ];

        $payment = new Mage_Sales_Model_Order_Payment();
        $payment->setMethod('checkmo');
        $order = new Mage_Sales_Model_Order();
        $order->setStoreId(1);
        $order->setBaseGrandTotal('25.00');
        $order->setPayment($payment);

        $sdkBuilder->expects(static::once())
            ->method('setPreferredLocation')
            ->with($preferredLocation);

        $builder->build($sdkBuilder, $order, $serviceInfo);
    }

    /**
     * @test
     * @loadFixture Model_ShipperConfigTest
     * @loadFixture Model_ShipmentConfigTest
     */
    public function buildVisualCheckOfAgeService()
    {
        $sdkBuilder = $this->getMockBuilder(\Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $builder = new Dhl_Versenden_Model_Webservice_Builder_Service([
            'shipper_config' => Mage::getModel('dhl_versenden/config_shipper'),
            'shipment_config' => Mage::getModel('dhl_versenden/config_shipment'),
            'service_config' => Mage::getModel('dhl_versenden/config_service'),
        ]);

        $ageCheck = 'A18';
        $serviceInfo = [
            'shipment_service' => [
                Service\VisualCheckOfAge::CODE => '1',
            ],
            'service_setting' => [
                Service\VisualCheckOfAge::CODE => $ageCheck,
            ],
        ];

        $payment = new Mage_Sales_Model_Order_Payment();
        $payment->setMethod('checkmo');
        $order = new Mage_Sales_Model_Order();
        $order->setStoreId(1);
        $order->setBaseGrandTotal('50.00');
        $order->setPayment($payment);

        $sdkBuilder->expects(static::once())
            ->method('setVisualCheckOfAge')
            ->with($ageCheck);

        $builder->build($sdkBuilder, $order, $serviceInfo);
    }

    /**
     * @test
     * @loadFixture Model_ShipperConfigTest
     * @loadFixture Model_ShipmentConfigTest
     * @loadFixture Model_ConfigTest
     */
    public function buildParcelOutletRoutingService()
    {
        $sdkBuilder = $this->getMockBuilder(\Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $builder = new Dhl_Versenden_Model_Webservice_Builder_Service([
            'shipper_config' => Mage::getModel('dhl_versenden/config_shipper'),
            'shipment_config' => Mage::getModel('dhl_versenden/config_shipment'),
            'service_config' => Mage::getModel('dhl_versenden/config_service'),
        ]);

        $serviceInfo = [
            'shipment_service' => [
                Service\ParcelOutletRouting::CODE => '1',
            ],
            'service_setting' => [],
        ];

        $shippingAddress = new Mage_Sales_Model_Order_Address();
        $shippingAddress->setEmail('customer@example.com');

        $payment = new Mage_Sales_Model_Order_Payment();
        $payment->setMethod('checkmo');
        $order = new Mage_Sales_Model_Order();
        $order->setStoreId(1);
        $order->setBaseGrandTotal('35.00');
        $order->setPayment($payment);
        $order->setShippingAddress($shippingAddress);

        // Should use customer's shipping email as fallback
        $sdkBuilder->expects(static::once())
            ->method('setParcelOutletRouting')
            ->with('customer@example.com');

        $builder->build($sdkBuilder, $order, $serviceInfo);
    }

    /**
     * @test
     * @loadFixture Model_ShipperConfigTest
     * @loadFixture Model_ShipmentConfigTest
     * @loadFixture Model_ConfigTest
     */
    public function buildParcelOutletRoutingUsesFormEmail()
    {
        $sdkBuilder = $this->getMockBuilder(\Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $builder = new Dhl_Versenden_Model_Webservice_Builder_Service([
            'shipper_config' => Mage::getModel('dhl_versenden/config_shipper'),
            'shipment_config' => Mage::getModel('dhl_versenden/config_shipment'),
            'service_config' => Mage::getModel('dhl_versenden/config_service'),
        ]);

        $formEmail = 'custom@form-input.com';
        $serviceInfo = [
            'shipment_service' => [
                Service\ParcelOutletRouting::CODE => '1',
            ],
            'service_setting' => [
                Service\ParcelOutletRouting::CODE => $formEmail,
            ],
        ];

        $shippingAddress = new Mage_Sales_Model_Order_Address();
        $shippingAddress->setEmail('customer@example.com');

        $payment = new Mage_Sales_Model_Order_Payment();
        $payment->setMethod('checkmo');
        $order = new Mage_Sales_Model_Order();
        $order->setStoreId(1);
        $order->setBaseGrandTotal('35.00');
        $order->setPayment($payment);
        $order->setShippingAddress($shippingAddress);

        // Form-submitted email must take priority over config and order email
        $sdkBuilder->expects(static::once())
            ->method('setParcelOutletRouting')
            ->with($formEmail);

        $builder->build($sdkBuilder, $order, $serviceInfo);
    }

    /**
     * @test
     * @loadFixture Model_ShipperConfigTest
     * @loadFixture Model_ShipmentConfigTest
     */
    public function buildWithEmptyServiceInfo()
    {
        $sdkBuilder = $this->getMockBuilder(\Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $builder = new Dhl_Versenden_Model_Webservice_Builder_Service([
            'shipper_config' => Mage::getModel('dhl_versenden/config_shipper'),
            'shipment_config' => Mage::getModel('dhl_versenden/config_shipment'),
            'service_config' => Mage::getModel('dhl_versenden/config_service'),
        ]);

        // Empty service info - no services should be called
        $serviceInfo = [];

        $payment = new Mage_Sales_Model_Order_Payment();
        $payment->setMethod('checkmo'); // non-COD
        $order = new Mage_Sales_Model_Order();
        $order->setStoreId(1);
        $order->setBaseGrandTotal('25.00');
        $order->setPayment($payment);

        // None of these should be called
        $sdkBuilder->expects(static::never())->method('setPreferredDay');
        $sdkBuilder->expects(static::never())->method('setPreferredLocation');
        $sdkBuilder->expects(static::never())->method('setPreferredNeighbour');
        $sdkBuilder->expects(static::never())->method('setVisualCheckOfAge');
        $sdkBuilder->expects(static::never())->method('setInsuredValue');
        $sdkBuilder->expects(static::never())->method('setBulkyGoods');
        $sdkBuilder->expects(static::never())->method('setParcelOutletRouting');
        $sdkBuilder->expects(static::never())->method('setCodAmount');
        $sdkBuilder->expects(static::never())->method('setGoGreenPlus');
        $sdkBuilder->expects(static::never())->method('setReturnShipmentGoGreenPlus');

        $builder->build($sdkBuilder, $order, $serviceInfo);
    }

    /**
     * @test
     * @loadFixture Model_ShipperConfigTest
     * @loadFixture Model_ShipmentConfigTest
     */
    public function buildWithoutPaymentDoesNotCrash()
    {
        $sdkBuilder = $this->getMockBuilder(\Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $builder = new Dhl_Versenden_Model_Webservice_Builder_Service([
            'shipper_config' => Mage::getModel('dhl_versenden/config_shipper'),
            'shipment_config' => Mage::getModel('dhl_versenden/config_shipment'),
            'service_config' => Mage::getModel('dhl_versenden/config_service'),
        ]);

        $serviceInfo = [];

        // Order without payment (getPayment() returns false)
        $order = new Mage_Sales_Model_Order();
        $order->setStoreId(1);
        $order->setBaseGrandTotal('25.00');
        // Intentionally NOT setting payment

        // COD should never be called since there's no payment
        $sdkBuilder->expects(static::never())->method('setCodAmount');

        // Should not crash
        $builder->build($sdkBuilder, $order, $serviceInfo);
    }

    /**
     * @test
     * @loadFixture Model_ShipperConfigTest
     * @loadFixture Model_ShipmentConfigTest
     */
    public function buildGoGreenPlusWithReturnShipment()
    {
        $sdkBuilder = $this->getMockBuilder(\Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $builder = new Dhl_Versenden_Model_Webservice_Builder_Service([
            'shipper_config' => Mage::getModel('dhl_versenden/config_shipper'),
            'shipment_config' => Mage::getModel('dhl_versenden/config_shipment'),
            'service_config' => Mage::getModel('dhl_versenden/config_service'),
        ]);

        $serviceInfo = [
            'shipment_service' => [
                Service\GoGreenPlus::CODE => '1',
                Service\ReturnShipment::CODE => '1',
            ],
        ];

        $payment = new Mage_Sales_Model_Order_Payment();
        $payment->setMethod('checkmo');
        $order = new Mage_Sales_Model_Order();
        $order->setStoreId(1);
        $order->setBaseGrandTotal('25.00');
        $order->setPayment($payment);

        $sdkBuilder->expects(static::once())
            ->method('setGoGreenPlus');
        $sdkBuilder->expects(static::once())
            ->method('setReturnShipmentGoGreenPlus');

        $builder->build($sdkBuilder, $order, $serviceInfo);
    }

    /**
     * @test
     * @loadFixture Model_ShipperConfigTest
     * @loadFixture Model_ShipmentConfigTest
     */
    public function buildGoGreenPlusWithoutReturnShipment()
    {
        $sdkBuilder = $this->getMockBuilder(\Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $builder = new Dhl_Versenden_Model_Webservice_Builder_Service([
            'shipper_config' => Mage::getModel('dhl_versenden/config_shipper'),
            'shipment_config' => Mage::getModel('dhl_versenden/config_shipment'),
            'service_config' => Mage::getModel('dhl_versenden/config_service'),
        ]);

        $serviceInfo = [
            'shipment_service' => [
                Service\GoGreenPlus::CODE => '1',
            ],
        ];

        $payment = new Mage_Sales_Model_Order_Payment();
        $payment->setMethod('checkmo');
        $order = new Mage_Sales_Model_Order();
        $order->setStoreId(1);
        $order->setBaseGrandTotal('25.00');
        $order->setPayment($payment);

        $sdkBuilder->expects(static::once())
            ->method('setGoGreenPlus');
        $sdkBuilder->expects(static::never())
            ->method('setReturnShipmentGoGreenPlus');

        $builder->build($sdkBuilder, $order, $serviceInfo);
    }

    /**
     * @test
     * @loadFixture Model_ShipperConfigTest
     * @loadFixture Model_ShipmentConfigTest
     */
    public function buildInsuranceIgnoresServiceSetting()
    {
        $sdkBuilder = $this->getMockBuilder(\Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $builder = new Dhl_Versenden_Model_Webservice_Builder_Service([
            'shipper_config' => Mage::getModel('dhl_versenden/config_shipper'),
            'shipment_config' => Mage::getModel('dhl_versenden/config_shipment'),
            'service_config' => Mage::getModel('dhl_versenden/config_service'),
        ]);

        $serviceInfo = [
            'shipment_service' => [
                Service\AdditionalInsurance::CODE => '1',
            ],
            'service_setting' => [
                Service\AdditionalInsurance::CODE => '5000',
            ],
        ];

        $payment = new Mage_Sales_Model_Order_Payment();
        $payment->setMethod('checkmo');
        $order = new Mage_Sales_Model_Order();
        $order->setStoreId(1);
        $order->setBaseGrandTotal('19.8600');
        $order->setPayment($payment);

        // Form value of 5000 must be ignored — always use order total
        $sdkBuilder->expects(static::once())
            ->method('setInsuredValue')
            ->with(19.86);

        $builder->build($sdkBuilder, $order, $serviceInfo);
    }

    /**
     * @test
     * @loadFixture Model_ShipperConfigTest
     * @loadFixture Model_ShipmentConfigTest
     */
    public function buildDeliveryTypeEconomy()
    {
        $sdkBuilder = $this->getMockBuilder(\Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $builder = new Dhl_Versenden_Model_Webservice_Builder_Service([
            'shipper_config' => Mage::getModel('dhl_versenden/config_shipper'),
            'shipment_config' => Mage::getModel('dhl_versenden/config_shipment'),
            'service_config' => Mage::getModel('dhl_versenden/config_service'),
        ]);

        $serviceInfo = [
            'shipment_service' => [
                Service\DeliveryType::CODE => '1',
            ],
            'service_setting' => [
                Service\DeliveryType::CODE => Service\DeliveryType::ECONOMY,
            ],
        ];

        $payment = new Mage_Sales_Model_Order_Payment();
        $payment->setMethod('checkmo');
        $order = new Mage_Sales_Model_Order();
        $order->setStoreId(1);
        $order->setBaseGrandTotal('25.00');
        $order->setPayment($payment);

        $sdkBuilder->expects(static::once())
            ->method('setDeliveryType')
            ->with(\Dhl\Sdk\ParcelDe\Shipping\Api\ShipmentOrderRequestBuilderInterface::DELIVERY_TYPE_ECONOMY);

        $builder->build($sdkBuilder, $order, $serviceInfo);
    }

    /**
     * @test
     * @loadFixture Model_ShipperConfigTest
     * @loadFixture Model_ShipmentConfigTest
     */
    public function buildDeliveryTypePremium()
    {
        $sdkBuilder = $this->getMockBuilder(\Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $builder = new Dhl_Versenden_Model_Webservice_Builder_Service([
            'shipper_config' => Mage::getModel('dhl_versenden/config_shipper'),
            'shipment_config' => Mage::getModel('dhl_versenden/config_shipment'),
            'service_config' => Mage::getModel('dhl_versenden/config_service'),
        ]);

        $serviceInfo = [
            'shipment_service' => [
                Service\DeliveryType::CODE => '1',
            ],
            'service_setting' => [
                Service\DeliveryType::CODE => Service\DeliveryType::PREMIUM,
            ],
        ];

        $payment = new Mage_Sales_Model_Order_Payment();
        $payment->setMethod('checkmo');
        $order = new Mage_Sales_Model_Order();
        $order->setStoreId(1);
        $order->setBaseGrandTotal('25.00');
        $order->setPayment($payment);

        $sdkBuilder->expects(static::once())
            ->method('setDeliveryType')
            ->with(\Dhl\Sdk\ParcelDe\Shipping\Api\ShipmentOrderRequestBuilderInterface::DELIVERY_TYPE_PREMIUM);

        $builder->build($sdkBuilder, $order, $serviceInfo);
    }

    /**
     * ClosestDropPoint checkout selection must map to DeliveryType CDP
     * in the autocreate flow where service data comes from versendenInfo.
     *
     * @test
     * @loadFixture Model_ShipperConfigTest
     * @loadFixture Model_ShipmentConfigTest
     */
    public function buildClosestDropPointMapsToCdp()
    {
        $sdkBuilder = $this->getMockBuilder(\Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $builder = new Dhl_Versenden_Model_Webservice_Builder_Service([
            'shipper_config' => Mage::getModel('dhl_versenden/config_shipper'),
            'shipment_config' => Mage::getModel('dhl_versenden/config_shipment'),
            'service_config' => Mage::getModel('dhl_versenden/config_service'),
        ]);

        $serviceInfo = [
            'shipment_service' => [
                Service\ClosestDropPoint::CODE => '1',
            ],
            'service_setting' => [],
        ];

        $payment = new Mage_Sales_Model_Order_Payment();
        $payment->setMethod('checkmo');
        $order = new Mage_Sales_Model_Order();
        $order->setStoreId(1);
        $order->setBaseGrandTotal('25.00');
        $order->setPayment($payment);

        $sdkBuilder->expects(static::once())
            ->method('setDeliveryType')
            ->with(\Dhl\Sdk\ParcelDe\Shipping\Api\ShipmentOrderRequestBuilderInterface::DELIVERY_TYPE_CDP);

        $builder->build($sdkBuilder, $order, $serviceInfo);
    }

    /**
     * @test
     * @loadFixture Model_ShipperConfigTest
     * @loadFixture Model_ShipmentConfigTest
     */
    public function buildDeliveryTypeCdp()
    {
        $sdkBuilder = $this->getMockBuilder(\Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $builder = new Dhl_Versenden_Model_Webservice_Builder_Service([
            'shipper_config' => Mage::getModel('dhl_versenden/config_shipper'),
            'shipment_config' => Mage::getModel('dhl_versenden/config_shipment'),
            'service_config' => Mage::getModel('dhl_versenden/config_service'),
        ]);

        $serviceInfo = [
            'shipment_service' => [
                Service\DeliveryType::CODE => '1',
            ],
            'service_setting' => [
                Service\DeliveryType::CODE => Service\DeliveryType::CDP,
            ],
        ];

        $payment = new Mage_Sales_Model_Order_Payment();
        $payment->setMethod('checkmo');
        $order = new Mage_Sales_Model_Order();
        $order->setStoreId(1);
        $order->setBaseGrandTotal('25.00');
        $order->setPayment($payment);

        $sdkBuilder->expects(static::once())
            ->method('setDeliveryType')
            ->with(\Dhl\Sdk\ParcelDe\Shipping\Api\ShipmentOrderRequestBuilderInterface::DELIVERY_TYPE_CDP);

        $builder->build($sdkBuilder, $order, $serviceInfo);
    }

    /**
     * Build a pDDP-capable service builder stub with controllable time and currency.
     *
     * @param int $timestamp
     * @param string $currencyCode
     * @return Dhl_Versenden_Test_Model_Webservice_Builder_ServiceStub
     */
    protected function createPddpBuilderStub($timestamp, $currencyCode)
    {
        $builder = new Dhl_Versenden_Test_Model_Webservice_Builder_ServiceStub([
            'shipper_config' => Mage::getModel('dhl_versenden/config_shipper'),
            'shipment_config' => Mage::getModel('dhl_versenden/config_shipment'),
            'service_config' => Mage::getModel('dhl_versenden/config_service'),
        ]);
        $builder->currentTimestamp = $timestamp;
        $builder->baseCurrencyCode = $currencyCode;

        return $builder;
    }

    /**
     * Create a sales order with the given destination country and value.
     *
     * @param string $countryId
     * @param float $baseGrandTotal
     * @return Mage_Sales_Model_Order
     */
    protected function createPddpOrder($countryId, $baseGrandTotal)
    {
        $address = Mage::getModel('sales/order_address');
        $address->setCountryId($countryId);

        $order = new Mage_Sales_Model_Order();
        $order->setStoreId(1);
        $order->setBaseGrandTotal($baseGrandTotal);
        $order->setShippingAddress($address);

        return $order;
    }

    /**
     * @return mixed[][]
     */
    public function getPddpAutoEnableDataProvider()
    {
        $before = Dhl_Versenden_Test_Model_Util_PddpThresholdsTest::BEFORE_CUTOVER;
        $at = Dhl_Versenden_Test_Model_Util_PddpThresholdsTest::AT_CUTOVER;

        return [
            // boundary is inclusive (DHL: bis einschließlich) — the < → <= fix
            'before_us_usd_800_enabled'      => [$before, 'USD', 'US', 800.00, true],
            'before_us_usd_800_01_disabled'  => [$before, 'USD', 'US', 800.01, false],
            'before_us_eur_680_enabled'      => [$before, 'EUR', 'US', 680.00, true],
            'before_us_eur_680_01_disabled'  => [$before, 'EUR', 'US', 680.01, false],
            // transition band 800-2500 USD is the business-critical case
            'before_us_usd_1200_disabled'    => [$before, 'USD', 'US', 1200.00, false],
            'from_us_usd_1200_enabled'       => [$at, 'USD', 'US', 1200.00, true],
            // new regime boundaries
            'from_us_usd_2500_enabled'       => [$at, 'USD', 'US', 2500.00, true],
            'from_us_usd_2500_01_disabled'   => [$at, 'USD', 'US', 2500.01, false],
            'from_us_eur_2125_enabled'       => [$at, 'EUR', 'US', 2125.00, true],
            'from_us_eur_2125_01_disabled'   => [$at, 'EUR', 'US', 2125.01, false],
            // Puerto Rico behaves like the USA in both regimes
            'before_pr_usd_800_enabled'      => [$before, 'USD', 'PR', 800.00, true],
            'from_pr_usd_1200_enabled'       => [$at, 'USD', 'PR', 1200.00, true],
            // other destinations and currencies never auto-enable
            'before_ch_usd_100_disabled'     => [$before, 'USD', 'CH', 100.00, false],
            'from_us_gbp_100_disabled'       => [$at, 'GBP', 'US', 100.00, false],
        ];
    }

    /**
     * Assert pDDP auto-enable honors the date-gated inclusive threshold and PR.
     *
     * @test
     * @dataProvider getPddpAutoEnableDataProvider
     *
     * @param int $timestamp
     * @param string $currencyCode
     * @param string $countryId
     * @param float $orderValue
     * @param bool $expectEnabled
     */
    public function pddpAutoEnable($timestamp, $currencyCode, $countryId, $orderValue, $expectEnabled)
    {
        $sdkBuilder = $this->getMockBuilder(\Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();
        $sdkBuilder->expects($expectEnabled ? static::once() : static::never())
            ->method('setDeliveryDutyPaid');

        $builder = $this->createPddpBuilderStub($timestamp, $currencyCode);
        $order = $this->createPddpOrder($countryId, $orderValue);

        $builder->build($sdkBuilder, $order, []);
    }

    /**
     * @return mixed[][]
     */
    public function getPddpGuardRejectionDataProvider()
    {
        $before = Dhl_Versenden_Test_Model_Util_PddpThresholdsTest::BEFORE_CUTOVER;
        $at = Dhl_Versenden_Test_Model_Util_PddpThresholdsTest::AT_CUTOVER;

        return [
            'before_us_usd_900'   => [$before, 'USD', 'US', 900.00],
            'from_us_usd_2500_01' => [$at, 'USD', 'US', 2500.01],
            'from_pr_eur_2125_01' => [$at, 'EUR', 'PR', 2125.01],
        ];
    }

    /**
     * Assert that manually selected pDDP above the active limit is rejected
     * instead of being passed to the DHL API.
     *
     * @test
     * @dataProvider getPddpGuardRejectionDataProvider
     *
     * @param int $timestamp
     * @param string $currencyCode
     * @param string $countryId
     * @param float $orderValue
     */
    public function pddpGuardRejectsAboveLimit($timestamp, $currencyCode, $countryId, $orderValue)
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('PDDP is not allowed for USA/Puerto Rico shipments');

        $sdkBuilder = $this->getMockBuilder(\Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $builder = $this->createPddpBuilderStub($timestamp, $currencyCode);
        $order = $this->createPddpOrder($countryId, $orderValue);

        $serviceInfo = [
            'shipment_service' => [Service\PostalDeliveryDutyPaid::CODE => '1'],
        ];
        $builder->build($sdkBuilder, $order, $serviceInfo);
    }

    /**
     * Assert that manual pDDP at exactly the limit is transmitted.
     *
     * @test
     */
    public function pddpGuardAllowsAtLimit()
    {
        $sdkBuilder = $this->getMockBuilder(\Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();
        $sdkBuilder->expects(static::once())->method('setDeliveryDutyPaid');

        $builder = $this->createPddpBuilderStub(
            Dhl_Versenden_Test_Model_Util_PddpThresholdsTest::AT_CUTOVER,
            'USD',
        );
        $order = $this->createPddpOrder('US', 2500.00);

        $serviceInfo = [
            'shipment_service' => [Service\PostalDeliveryDutyPaid::CODE => '1'],
        ];
        $builder->build($sdkBuilder, $order, $serviceInfo);
    }

    /**
     * Assert that manual pDDP for non-US-territory destinations passes through
     * unguarded (thresholds only apply to the US customs territory).
     *
     * @test
     */
    public function pddpGuardIgnoresOtherDestinations()
    {
        $sdkBuilder = $this->getMockBuilder(\Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();
        $sdkBuilder->expects(static::once())->method('setDeliveryDutyPaid');

        $builder = $this->createPddpBuilderStub(
            Dhl_Versenden_Test_Model_Util_PddpThresholdsTest::BEFORE_CUTOVER,
            'USD',
        );
        $order = $this->createPddpOrder('CH', 99999.00);

        $serviceInfo = [
            'shipment_service' => [Service\PostalDeliveryDutyPaid::CODE => '1'],
        ];
        $builder->build($sdkBuilder, $order, $serviceInfo);
    }

    /**
     * Assert that manual pDDP for US territory from a store with unsupported
     * base currency is rejected with an understandable message.
     *
     * @test
     */
    public function pddpGuardRejectsUnsupportedCurrency()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('EUR or USD');

        $sdkBuilder = $this->getMockBuilder(\Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $builder = $this->createPddpBuilderStub(
            Dhl_Versenden_Test_Model_Util_PddpThresholdsTest::AT_CUTOVER,
            'GBP',
        );
        $order = $this->createPddpOrder('US', 100.00);

        $serviceInfo = [
            'shipment_service' => [Service\PostalDeliveryDutyPaid::CODE => '1'],
        ];
        $builder->build($sdkBuilder, $order, $serviceInfo);
    }
}

/**
 * Stub with controllable clock and store currency (project rule: no reflection
 * in tests — override the accessors instead).
 */
class Dhl_Versenden_Test_Model_Webservice_Builder_ServiceStub extends Dhl_Versenden_Model_Webservice_Builder_Service
{
    /** @var int */
    public $currentTimestamp = 0;

    /** @var string */
    public $baseCurrencyCode = 'USD';

    protected function _getCurrentTimestamp()
    {
        return $this->currentTimestamp;
    }

    protected function _getBaseCurrencyCode(Mage_Core_Model_Abstract $salesEntity)
    {
        return $this->baseCurrencyCode;
    }
}
