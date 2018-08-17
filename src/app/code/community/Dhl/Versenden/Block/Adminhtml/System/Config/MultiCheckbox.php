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
 * @author    Andreas Müller <andreas.mueller@netresearch.de>
 * @copyright 2018 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://www.netresearch.de/
 */

/**
 * Dhl_Versenden_Block_Adminhtml_System_Config_MultiCheckbox
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Andreas Müller <andreas.mueller@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     https://www.netresearch.de/
 */
class Dhl_Versenden_Block_Adminhtml_System_Config_MultiCheckbox extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * @var string[]|null
     */
    protected $values = null;

    protected function _construct()
    {
        $this->setTemplate('dhl_versenden/system/config/multicheckboxes.phtml');

        parent::_construct();
    }

    /**
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setNamePrefix($element->getName())
            ->setHtmlId($element->getHtmlId());

        return $this->_toHtml();
    }

    /**
     * @return array
     */
    public function getValues()
    {
        return Mage::getSingleton('dhl_versenden/adminhtml_system_config_source_dropOffDays')
            ->toOptionArray();
    }

    /**
     * @param $name
     * @return bool
     * @throws Mage_Core_Model_Store_Exception
     */
    public function isChecked($name)
    {
        if (!is_array($this->getCheckedValues())) {
            return false;
        }

        return in_array((string)$name, $this->getCheckedValues(), true);
    }

    /**
     * @return string[]
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getCheckedValues()
    {
        if ($this->values === null) {
            $data = $this->getConfigData('carriers/dhlversenden/drop_off_days');
            if ($data) {
                $this->values = explode(',', $data);
            }
        }

        return $this->values;
    }

}
