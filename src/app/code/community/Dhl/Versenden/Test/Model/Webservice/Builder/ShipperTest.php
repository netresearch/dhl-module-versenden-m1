<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Test_Model_Webservice_Builder_ShipperTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     */
    public function constructorArgShipperConfigMissing()
    {
        $this->expectException(Mage_Core_Exception::class);

        new Dhl_Versenden_Model_Webservice_Builder_Shipper([
        ]);
    }

    /**
     * @test
     */
    public function constructorArgShipperConfigWrongType()
    {
        $this->expectException(Mage_Core_Exception::class);

        new Dhl_Versenden_Model_Webservice_Builder_Shipper([
            'config' => new stdClass(),
        ]);
    }

    /**
     * @test
     * @loadFixture Model_ShipperConfigTest
     */
    public function buildShipper()
    {
        // Create a mock SDK builder to verify method calls
        $sdkBuilder = $this->getMockBuilder(\Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Expect setShipperAccount to be called
        $sdkBuilder->expects(static::once())
            ->method('setShipperAccount')
            ->with(static::anything(), static::anything());

        // Expect setShipperAddress to be called
        $sdkBuilder->expects(static::once())
            ->method('setShipperAddress')
            ->with(
                static::anything(), // company
                static::anything(), // countryCode
                static::anything(), // postalCode
                static::anything(), // city
                static::anything(), // streetName
                static::anything(), // streetNumber
                static::anything(), // name
                static::anything(), // nameAddition
                static::anything(), // email
                static::anything(), // phone
                static::anything(), // contactPerson
                static::anything(), // state
                static::anything(), // dispatchingInformation
                static::anything(),  // addressAddition
            );

        $builder = new Dhl_Versenden_Model_Webservice_Builder_Shipper([
            'config' => Mage::getModel('dhl_versenden/config_shipper'),
        ]);

        // Call the new build method (returns void)
        // 3rd arg is productCode, required for billing number calculation
        $builder->build($sdkBuilder, 2, 'V01PAK');
    }

    /**
     * @test
     * @loadFixture Model_ShipperConfigTest
     */
    public function setShipmentReturnsSelf()
    {
        $builder = new Dhl_Versenden_Model_Webservice_Builder_Shipper([
            'config' => Mage::getModel('dhl_versenden/config_shipper'),
        ]);

        $order = new Mage_Sales_Model_Order();
        $order->setIncrementId('100000001');
        $order->setCustomerId(42);

        $shipment = new Mage_Sales_Model_Order_Shipment();
        $shipment->setOrder($order);

        $result = $builder->setShipment($shipment);

        // setShipment should return $this for method chaining
        static::assertSame($builder, $result);
    }

    /**
     * @test
     * @loadFixture Model_ShipperConfigTest
     */
    public function buildWithShipmentEnablesBankRefMap()
    {
        // Create a mock SDK builder to verify method calls
        $sdkBuilder = $this->getMockBuilder(\Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        // These methods will be called
        $sdkBuilder->expects(static::once())
            ->method('setShipperAccount')
            ->with(static::anything(), static::anything());

        $sdkBuilder->expects(static::once())
            ->method('setShipperAddress')
            ->with(
                static::anything(), // company
                static::anything(), // countryCode
                static::anything(), // postalCode
                static::anything(), // city
                static::anything(), // streetName
                static::anything(), // streetNumber
                static::anything(), // name
                static::anything(), // nameAddition
                static::anything(), // email
                static::anything(), // phone
                static::anything(), // contactPerson
                static::anything(), // state
                static::anything(), // dispatchingInformation
                static::anything(),  // addressAddition
            );

        $sdkBuilder->expects(static::once())
            ->method('setReturnAddress')
            ->with(
                static::anything(), // company
                static::anything(), // countryCode
                static::anything(), // postalCode
                static::anything(), // city
                static::anything(), // streetName
                static::anything(), // streetNumber
                static::anything(), // name
                static::anything(), // nameAddition
                static::anything(), // email
                static::anything(), // phone
                static::anything(), // contactPerson
                static::anything(), // state
                static::anything(), // dispatchingInformation
                static::anything(),  // addressAddition
            );

        $builder = new Dhl_Versenden_Model_Webservice_Builder_Shipper([
            'config' => Mage::getModel('dhl_versenden/config_shipper'),
        ]);

        // Set up shipment with order data for bank ref map
        $order = new Mage_Sales_Model_Order();
        $order->setIncrementId('100000042');
        $order->setCustomerId(99);

        $shipment = new Mage_Sales_Model_Order_Shipment();
        $shipment->setOrder($order);

        // Chain setShipment then build — pass true to include return shipment
        $builder->setShipment($shipment);
        $builder->build($sdkBuilder, 2, 'V01PAK', true);
    }

    /**
     * @test
     * @loadFixture Model_ShipperConfigTest
     */
    public function buildWithReturnShipmentDisabled()
    {
        $sdkBuilder = $this->getMockBuilder(\Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Return billing number should be null when return shipment is disabled
        $sdkBuilder->expects(static::once())
            ->method('setShipperAccount')
            ->with(static::anything(), static::isNull());

        // Return address should never be set
        $sdkBuilder->expects(static::never())
            ->method('setReturnAddress');

        $sdkBuilder->expects(static::once())
            ->method('setShipperAddress')
            ->with(
                static::anything(), static::anything(), static::anything(),
                static::anything(), static::anything(), static::anything(),
                static::anything(), static::anything(), static::anything(),
                static::anything(), static::anything(), static::anything(),
                static::anything(), static::anything(),
            );

        $builder = new Dhl_Versenden_Model_Webservice_Builder_Shipper([
            'config' => Mage::getModel('dhl_versenden/config_shipper'),
        ]);

        // V01PAK has a return procedure, but includeReturnShipment=false
        $builder->build($sdkBuilder, 2, 'V01PAK', false);
    }

    /**
     * @test
     * @loadFixture Model_ShipperConfigTest
     */
    public function buildWithReturnShipmentEnabled()
    {
        $sdkBuilder = $this->getMockBuilder(\Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Return billing number should be non-null when return shipment is enabled
        $sdkBuilder->expects(static::once())
            ->method('setShipperAccount')
            ->with(static::anything(), static::logicalNot(static::isNull()));

        // Return address should be set
        $sdkBuilder->expects(static::once())
            ->method('setReturnAddress')
            ->with(
                static::anything(), static::anything(), static::anything(),
                static::anything(), static::anything(), static::anything(),
                static::anything(), static::anything(), static::anything(),
                static::anything(), static::anything(), static::anything(),
                static::anything(), static::anything(),
            );

        $sdkBuilder->expects(static::once())
            ->method('setShipperAddress')
            ->with(
                static::anything(), static::anything(), static::anything(),
                static::anything(), static::anything(), static::anything(),
                static::anything(), static::anything(), static::anything(),
                static::anything(), static::anything(), static::anything(),
                static::anything(), static::anything(),
            );

        $builder = new Dhl_Versenden_Model_Webservice_Builder_Shipper([
            'config' => Mage::getModel('dhl_versenden/config_shipper'),
        ]);

        // V01PAK has a return procedure, and includeReturnShipment=true
        $builder->build($sdkBuilder, 2, 'V01PAK', true);
    }

    /**
     * @test
     * @loadFixture Model_ShipperConfigTest
     */
    public function buildInternationalProductIgnoresReturnFlag()
    {
        $sdkBuilder = $this->getMockBuilder(\Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        // International product has no return procedure — return address should never be set
        // even when includeReturnShipment=true
        $sdkBuilder->expects(static::once())
            ->method('setShipperAccount')
            ->with(static::anything(), static::isNull());

        $sdkBuilder->expects(static::never())
            ->method('setReturnAddress');

        $sdkBuilder->expects(static::once())
            ->method('setShipperAddress')
            ->with(
                static::anything(), static::anything(), static::anything(),
                static::anything(), static::anything(), static::anything(),
                static::anything(), static::anything(), static::anything(),
                static::anything(), static::anything(), static::anything(),
                static::anything(), static::anything(),
            );

        $builder = new Dhl_Versenden_Model_Webservice_Builder_Shipper([
            'config' => Mage::getModel('dhl_versenden/config_shipper'),
        ]);

        // V53WPAK has no return procedure
        $builder->build($sdkBuilder, 2, 'V53WPAK', true);
    }
}
