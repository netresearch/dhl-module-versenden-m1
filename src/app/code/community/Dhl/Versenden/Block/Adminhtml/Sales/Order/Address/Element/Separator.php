<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Block_Adminhtml_Sales_Order_Address_Element_Separator extends Varien_Data_Form_Element_Abstract
{
    /**
     * @param string $_idSuffix
     * @return string
     */
    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
    public function getLabelHtml($_idSuffix = '')
    {
        $value = $this->getData('value');
        if ($value) {
            $html = $value;
        } else {
            $html = '<hr/>';
        }

        return $html;
    }

    /**
     * @return string
     */
    public function getElementHtml()
    {
        $html = '';
        $html .= $this->getAfterElementHtml();

        return $html;
    }
}
