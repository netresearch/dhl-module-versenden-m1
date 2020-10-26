<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Block_Adminhtml_System_Config_MultiCheckbox extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setTemplate('dhl_versenden/system/config/multicheckboxes.phtml');
        $this->setData('element', $element);

        return $this->_toHtml();
    }

    /**
     * @return Varien_Data_Form_Element_Abstract
     */
    public function getElement()
    {
        return $this->getData('element');
    }

    /**
     * @param Varien_Data_Form_Element_Abstract $element
     * @return mixed[]
     */
    public function getSelectedValues(Varien_Data_Form_Element_Abstract $element)
    {
        $value = $element->getData('value');
        if (!is_array($value)) {
            $value = explode(',', $value);
        }

        return $value;
    }
}
