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
 * @copyright 2019 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */

/**
 * Dhl_Versenden_Block_Adminhtml_Sales_Order_Grid
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     https://www.netresearch.de/
 */
class Dhl_Versenden_Block_Adminhtml_Sales_Order_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Render block HTML
     *
     * Since M1.9.4.3 filter callback methods are only executed on instances
     * of `Mage_Adminhtml_Block_Widget_Grid`. Applying the label status filter
     * is the only use of this block, it is not meant to output anything.
     *
     * @see \Mage_Adminhtml_Block_Widget_Grid::_addColumnFilterToCollection
     * @link https://magento.stackexchange.com/a/292307
     *
     * @return string
     */
    protected function _toHtml()
    {
        return '';
    }

    /**
     * Filter grid by DHL label status
     *
     * @param Mage_Sales_Model_Resource_Order_Grid_Collection $collection
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return void
     */
    public function filterStatus(
        Mage_Sales_Model_Resource_Order_Grid_Collection $collection,
        Mage_Adminhtml_Block_Widget_Grid_Column $column
    ) {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }

        $collection->join(
            array('status' => 'dhl_versenden/label_status'),
            'main_table.entity_id = status.order_id',
            array('status_code')
        );
        $collection->addFieldToFilter('status_code', array('eq' => $value));
    }
}
