<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Test_Block_Adminhtml_Sales_Order_Shipment_CustomsTest extends EcomDev_PHPUnit_Test_Case
{
    public const BLOCK_ALIAS = 'dhl_versenden/adminhtml_sales_order_shipment_customs';

    protected function mockCustomsBlock()
    {
        $shippingAddress = Mage::getModel('sales/order_address');
        $shippingAddress->setCountryId('DE');

        $order = Mage::getModel('sales/order');
        $order->setShippingAddress($shippingAddress);

        $shipment = Mage::getModel('sales/order_shipment');
        $shipment->setStoreId(1);
        $shipment->setOrder($order);

        $editBlockMock = $this->getBlockMock(self::BLOCK_ALIAS, ['getShipment', 'fetchView']);
        $editBlockMock
            ->expects(static::any())
            ->method('getShipment')
            ->willReturn($shipment);
        $this->replaceByMock('block', self::BLOCK_ALIAS, $editBlockMock);
    }

    /**
     * @test
     */
    public function getShipment()
    {
        $shipment = 'foo';
        Mage::register('current_shipment', $shipment);
        $block = new Dhl_Versenden_Block_Adminhtml_Sales_Order_Shipment_Customs();
        static::assertSame($shipment, $block->getShipment());
        Mage::unregister('current_shipment');
    }

    /**
     * @test
     */
    public function getTermsOfTrade()
    {
        $fooTerm = 'foo';
        $barTerm = 'bar';
        $carrierTerms = [
            $fooTerm => $fooTerm,
            $barTerm => $barTerm,
        ];

        $carrierMock = $this->getModelMock('dhl_versenden/shipping_carrier_versenden', ['getCode']);
        $carrierMock
            ->expects(static::once())
            ->method('getCode')
            ->with(static::equalTo('terms_of_trade'))
            ->willReturn($carrierTerms);
        $this->replaceByMock('singleton', 'dhl_versenden/shipping_carrier_versenden', $carrierMock);

        /** @var Dhl_Versenden_Block_Adminhtml_Sales_Order_Shipment_Customs_Edit $block */
        $block = Mage::app()->getLayout()->createBlock(self::BLOCK_ALIAS);
        $blockTerms = $block->getTermsOfTrade();
        static::assertIsArray($blockTerms);
        static::assertCount(1 + count($carrierTerms), $blockTerms);


        foreach ($blockTerms as $blockTerm) {
            static::assertArrayHasKey('value', $blockTerm);
            static::assertArrayHasKey('label', $blockTerm);

            if ($blockTerm['value']) {
                static::assertContains($blockTerm['value'], $carrierTerms);
            }
        }
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function getCustomValueCurrencyCode()
    {
        $this->mockCustomsBlock();

        $currencyCode = 'xxx';

        /** @var EcomDev_PHPUnit_Mock_Proxy|Dhl_Versenden_Block_Adminhtml_Sales_Order_Shipment_Customs_Edit $block */
        $block = Mage::app()->getLayout()->createBlock(self::BLOCK_ALIAS);
        $block->getShipment()->getOrder()->setBaseCurrencyCode($currencyCode);

        static::assertEquals($currencyCode, $block->getCustomValueCurrencyCode());
    }
}
