<?php

/**
 * See LICENSE.md for license details.
 */

/**
 * Destination countries covered by the US customs territory rules
 * (10-digit HTSUS code, pDDP thresholds): USA and Puerto Rico.
 *
 * Puerto Rico appears as its own country code in Magento's country list;
 * a US address with region PR is covered by the 'US' entry.
 */
class Dhl_Versenden_Model_Util_UsCustomsTerritory
{
    public const COUNTRY_CODES = ['US', 'PR'];
}
