<?php

/**
 * See LICENSE.md for license details.
 */

/**
 * Upgrade script from version 1.14.0 to 2.0.0
 *
 * Removes obsolete SOAP API configuration fields that are no longer used
 * in version 2.0.0 which migrates to the REST API.
 *
 * @category  Dhl
 * @package   Dhl_Versenden
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
if (!isset($installer)) {
    $installer = $this;
}

// Note: SOAP username/password are NOT removed - they are reused for REST API authentication
// (DHL Business Customer Portal credentials work for both SOAP and REST)

// Remove obsolete SOAP endpoint configuration
$installer->deleteConfigData('carriers/dhlversenden/sandbox_endpoint');
$installer->deleteConfigData('carriers/dhlversenden/production_endpoint');
