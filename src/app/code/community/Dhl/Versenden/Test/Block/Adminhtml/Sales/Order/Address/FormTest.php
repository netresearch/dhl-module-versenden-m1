<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Test_Block_Adminhtml_Sales_Order_Address_FormTest
    extends EcomDev_PHPUnit_Test_Case
{
    const BLOCK_ALIAS = 'dhl_versenden/adminhtml_sales_order_address_form';

    protected function setUp()
    {
        parent::setUp();

        $storeCode = 'store_one';

        $blockMock = $this->getBlockMock(self::BLOCK_ALIAS, array('getStore', 'fetchView'));
        $blockMock
            ->expects($this->any())
            ->method('getStore')
            ->willReturn($storeCode);
        $this->replaceByMock('block', self::BLOCK_ALIAS, $blockMock);

        $quoteSessionMock = $this->getModelMock('adminhtml/session_quote', array('init', 'getStoreId'));
        $quoteSessionMock
            ->expects($this->any())
            ->method('getStoreId')
            ->willReturn($storeCode);
        $this->replaceByMock('singleton', 'adminhtml/session_quote', $quoteSessionMock);

        $coreSessionMock = $this->getModelMock('core/session', array('init'));
        $this->replaceByMock('singleton', 'core/session', $coreSessionMock);
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     * @registry order_address
     */
    public function getForm()
    {
        $info = new \Dhl\Versenden\Bcs\Api\Info();

        $stationId = '808';
        $postNumber = '12345678';
        $streetNumber = '303';
        $postalFacility = array(
            'packstation_number' => $stationId,
            'post_number' => $postNumber,
        );
        $receiver = array(
            'street_number' => $streetNumber,
            'packstation' => $postalFacility,
        );

        $info->getReceiver()->fromArray($receiver);

        $address = Mage::getModel('sales/order_address');
        $address->setData('dhl_versenden_info', $info);

        $this->replaceRegistry('order_address', $address);

        $block = Mage::app()->getLayout()->createBlock('dhl_versenden/adminhtml_sales_order_address_form');
        $form = $block->getForm();
        /** @var Varien_Data_Form_Element_Fieldset $fieldset */
        $fieldset = $form->getElement('main');

        /** @var Varien_Data_Form_Element_Abstract $element */
        $element = $fieldset->getElements()->searchById('versenden_info_street');
        $this->assertInstanceOf(
            Dhl_Versenden_Block_Adminhtml_Sales_Order_Address_Element_Separator::class,
            $element
        );

        $separatorLabelHtml = '';
        $element->setValue($separatorLabelHtml);
        $this->assertEquals('<hr/>', $element->getLabelHtml());

        $separatorLabelHtml = 'foo';
        $element->setValue($separatorLabelHtml);
        $this->assertEquals($separatorLabelHtml, $element->getLabelHtml());

        $separatorElementHtml = '';
        $element->setData('after_element_html', $separatorElementHtml);
        $this->assertEmpty($element->getElementHtml());

        $separatorElementHtml = 'bar';
        $element->setData('after_element_html', $separatorElementHtml);
        $this->assertEquals($separatorElementHtml, $element->getElementHtml());

        $element = $fieldset->getElements()->searchById('versenden_info_packstation_number');
        $this->assertInstanceOf(Varien_Data_Form_Element_Abstract::class, $element);
        $this->assertEquals($stationId, $element->getValue());

        $element = $fieldset->getElements()->searchById('versenden_info_packstation_post_number');
        $this->assertInstanceOf(Varien_Data_Form_Element_Abstract::class, $element);
        $this->assertEquals($postNumber, $element->getValue());

        $element = $fieldset->getElements()->searchById('versenden_info_street_number');
        $this->assertInstanceOf(Varien_Data_Form_Element_Abstract::class, $element);
        $this->assertEquals($streetNumber, $element->getValue());
    }
}
