<?php

/**
 * See LICENSE.md for license details.
 */

$installer = Mage::getResourceModel('sales/setup', 'sales_setup');

$attributeCode = 'dhl_versenden_info';
$installer->addAttribute('quote_address', $attributeCode, ['type' => 'text']);
$installer->addAttribute('order_address', $attributeCode, ['type' => 'text']);
