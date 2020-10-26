<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Block_Adminhtml_System_Config_Heading
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * Render element html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $comment = $element->getComment();
        if ($comment) {
            $comment = "<p>$comment</p>";
        }

        $html = sprintf('<td colspan="5"><h4>%s</h4>%s</td>', $element->getLabel(), $comment);
        return $this->_decorateRowHtml($element, $html);
    }

    /**
     * Decorate field row html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @param string $html
     * @return string
     */
    protected function _decorateRowHtml($element, $html)
    {
        return '<tr class="system-fieldset-sub-head" id="row_' . $element->getHtmlId() . '">' . $html . '</tr>';
    }
}
