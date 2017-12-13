<?php
/**
 * Dhl Versenden
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to
 * newer versions in the future.
 *
 * PHP version 5
 *
 * @category  Dhl
 * @package   Dhl_Versenden
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */

/**
 * Dhl_Versenden_Block_Adminhtml_Form_Field_Participation
 * DHL Versenden participation numbers combined form field (frontend model)
 *
 * TODO(nr): set proper id to grid in order to enable field dependencies
 * @see template/system/config/form/field/array.phtml
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Block_Adminhtml_Form_Field_Participation
    extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    /**
     * @var Mage_Core_Block_Html_Select
     */
    protected $_templateRenderer;


    /**
     * Create renderer used for displaying the country select element
     *
     * @return Mage_Core_Block_Html_Select
     */
    protected function _getTemplateRenderer()
    {
        if (!$this->_templateRenderer) {
            $this->_templateRenderer = $this->getLayout()->createBlock(
                'dhl_versenden/adminhtml_form_field_procedure_select',
                '',
                array('is_render_to_js_template' => true)
            );

            /** @var Dhl_Versenden_Model_Adminhtml_System_Config_Source_Procedure $sourceModel */
            $sourceModel = Mage::getModel('dhl_versenden/adminhtml_system_config_source_procedure');
            $this->_templateRenderer->setOptions($sourceModel->toOptionArray());
        }

        return $this->_templateRenderer;
    }

    /**
     * @see Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract::_prepareArrayRow()
     * @param Varien_Object $row
     */
    protected function _prepareArrayRow(Varien_Object $row)
    {
        $row->setData(
            'option_extra_attr_' . $this->_getTemplateRenderer()->calcOptionHash($row->getData('procedure')),
            'selected="selected"'
        );

        parent::_prepareArrayRow($row);
    }

    /**
     * (non-PHPdoc)
     * @see Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract::_prepareToRender()
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            'procedure', array(
            'label' => $this->__('Procedure'),
            'renderer' => $this->_getTemplateRenderer()
            )
        );
        $this->addColumn(
            'participation', array(
            'label' => $this->__('Participation'),
            'style' => 'width:80px',
            'class' => 'input-text required-entry'
            )
        );
        // hide "Add after" button
        $this->_addAfter = false;

        return parent::_prepareToRender();
    }
}
