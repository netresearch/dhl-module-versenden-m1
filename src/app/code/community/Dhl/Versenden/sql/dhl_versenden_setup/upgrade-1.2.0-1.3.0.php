<?php

/**
 * See LICENSE.md for license details.
 */

/** @var Mage_Sales_Model_Resource_Setup $installer */
$installer = Mage::getResourceModel('sales/setup', 'sales_setup');

$idColumnDefinition = [
    'identity'  => false,
    'unsigned'  => true,
    'nullable'  => false,
    'primary'   => true,
];

$statusColumnDefinition = [
    'default' => 0,
    'unsigned' => true,
    'nullable' => false,
];

$table = $installer->getConnection()
    ->newTable($installer->getTable('dhl_versenden/label_status'))
    ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, $idColumnDefinition, 'Entity Id')
    ->addColumn('status_code', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, $statusColumnDefinition, 'Status Code')
    ->addIndex(
        $installer->getIdxName('dhl_versenden/label_status', ['status_code']),
        ['status_code'],
    )
    ->addForeignKey(
        $installer->getFkName('dhl_versenden/label_status', 'order_id', 'sales/order', 'entity_id'),
        'order_id',
        $installer->getTable('sales/order'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE,
    )
    ->setComment('DHL Versenden Label Status');
$installer->getConnection()->createTable($table);
