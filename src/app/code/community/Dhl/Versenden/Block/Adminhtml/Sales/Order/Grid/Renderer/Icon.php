<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Block_Adminhtml_Sales_Order_Grid_Renderer_Icon
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $status = parent::render($row);
        if (!$status) {
            // no status, no status to display.
            return Mage::helper('dhl_versenden/data')->__('Not Available');
        }

        $format = '<img src="%s" alt="| %s" title="%s" class="dhl-status-icon"/>';
        if ($status == \Dhl_Versenden_Model_Label_Status::CODE_PROCESSED) {
            $src   = $this->getSkinUrl('images/dhl_versenden/icon_complete.png');
            $alt   = Mage::helper('dhl_versenden/data')->__('DHL Label Status (processed)');
            $title = Mage::helper('dhl_versenden/data')->__('Processed');
        } elseif ($status == \Dhl_Versenden_Model_Label_Status::CODE_FAILED) {
            $src   = $this->getSkinUrl('images/dhl_versenden/icon_failed.png');
            $alt   = Mage::helper('dhl_versenden/data')->__('DHL Label Status (failed)');
            $title = Mage::helper('dhl_versenden/data')->__('Status_Failed');
        } else {
            $src   = $this->getSkinUrl('images/dhl_versenden/icon_incomplete.png');
            $alt   = Mage::helper('dhl_versenden/data')->__('DHL Label Status (pending)');
            $title = Mage::helper('dhl_versenden/data')->__('Pending');
        }

        return sprintf($format, $src, $alt, $title);
    }

    /**
     * @param Varien_Object $row
     * @return string
     */
    public function renderExport(Varien_Object $row)
    {
        $status = parent::render($row);
        if (!$status) {
            // no status, no status to display.
            return Mage::helper('dhl_versenden/data')->__('Not Available');
        }

        if ($status == \Dhl_Versenden_Model_Label_Status::CODE_PROCESSED) {
            $title = Mage::helper('dhl_versenden/data')->__('Processed');
        } elseif ($status == \Dhl_Versenden_Model_Label_Status::CODE_FAILED) {
            $title = Mage::helper('dhl_versenden/data')->__('Failed');
        } else {
            $title = Mage::helper('dhl_versenden/data')->__('Pending');
        }

        return $title;
    }
}
