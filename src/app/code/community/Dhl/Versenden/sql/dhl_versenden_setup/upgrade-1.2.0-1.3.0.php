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
 * @copyright 2017 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */

/** @var Mage_Sales_Model_Resource_Setup $installer */
$installer = Mage::getResourceModel('sales/setup', 'sales_setup');

$idColumnDefinition = array(
    'identity'  => false,
    'unsigned'  => true,
    'nullable'  => false,
    'primary'   => true,
);

$statusColumnDefinition = array(
    'default' => 0,
    'unsigned' => true,
    'nullable' => false,
);

$table = $installer->getConnection()
    ->newTable($installer->getTable('dhl_versenden/label_status'))
    ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, $idColumnDefinition, 'Entity Id')
    ->addColumn('status_code', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, $statusColumnDefinition, 'Status Code')
    ->addIndex(
        $installer->getIdxName('dhl_versenden/label_status', array('status_code')),
        array('status_code')
    )
    ->addForeignKey(
        $installer->getFkName('dhl_versenden/label_status', 'order_id', 'sales/order', 'entity_id'),
        'order_id',
        $installer->getTable('sales/order'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('DHL Versenden Label Status');
$installer->getConnection()->createTable($table);
