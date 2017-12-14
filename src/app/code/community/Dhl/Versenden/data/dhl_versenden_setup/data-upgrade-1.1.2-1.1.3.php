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
        array('value' => $encryptedValue),
        array('config_id = ?' => $row['config_id'])
    );
}
