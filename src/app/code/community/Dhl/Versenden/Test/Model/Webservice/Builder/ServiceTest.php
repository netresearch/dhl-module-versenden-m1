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
}
