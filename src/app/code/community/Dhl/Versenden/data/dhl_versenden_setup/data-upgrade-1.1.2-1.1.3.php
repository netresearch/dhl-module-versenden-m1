<?php
/**
 * Created by PhpStorm.
 * User: andreas
 * Date: 08.12.17
 * Time: 09:18
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$select = $installer->_conn->select();
$select
    ->from($this->getTable('core_config_data'))
    ->where('path = ?', 'carriers/dhlversenden/account_signature');

$rows = $select->query();
foreach ($rows as $row) {
    $encryptedValue = Mage::helper('core/data')->encrypt($row['value']);
    $installer->_conn->update(
        $this->getTable('core_config_data'),
        array('value' => $encryptedValue),
        array('config_id = ?' => $row['config_id'])
    );
}
