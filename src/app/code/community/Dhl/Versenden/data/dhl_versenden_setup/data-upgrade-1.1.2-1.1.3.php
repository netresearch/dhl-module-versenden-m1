<?php

/**
 * See LICENSE.md for license details.
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$select = $installer->getConnection()->select();
$select
    ->from($this->getTable('core_config_data'))
    ->where('path = ?', 'carriers/dhlversenden/account_signature');

$rows = $select->query();
foreach ($rows as $row) {
    $encryptedValue = Mage::helper('core/data')->encrypt($row['value']);
    $installer->getConnection()->update(
        $this->getTable('core_config_data'),
        ['value' => $encryptedValue],
        ['config_id = ?' => $row['config_id']],
    );
}
