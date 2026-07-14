<?php

/**
 * See LICENSE.md for license details.
 */


class Dhl_Versenden_Model_Webservice_Builder_Customs
{
    /** @var string */
    protected $_unitOfMeasure;

    /** @var float */
    protected $_minWeightInKG;

    /**
     * Dhl_Versenden_Model_Webservice_Builder_Customs constructor.
     * @param mixed[] $args
     * @throws Mage_Core_Exception
     */
    public function __construct($args)
    {
        $argName = 'unit_of_measure';
        if (!isset($args[$argName])) {
            Mage::throwException("required argument missing: $argName");
        }

        if (!is_string($args[$argName])) {
            Mage::throwException("invalid argument: $argName");
        }

        $this->_unitOfMeasure = $args[$argName];

        $argName = 'min_weight';
        if (!isset($args[$argName])) {
            Mage::throwException("required argument missing: $argName");
        }

        if (!is_numeric($args[$argName])) {
            Mage::throwException("invalid argument: $argName");
        }

        $this->_minWeightInKG = $args[$argName];
    }

    /**
     * Build customs data into SDK request builder
     *
     * @param \Dhl\Sdk\ParcelDe\Shipping\Api\ShipmentOrderRequestBuilderInterface $sdkBuilder
     * @param string $invoiceNumber
     * @param string[] $customsInfo
     * @param string[] $packageInfo
     * @param string $recipientCountryId destination country (ISO-2)
     * @return void
     */
    public function build(\Dhl\Sdk\ParcelDe\Shipping\Api\ShipmentOrderRequestBuilderInterface $sdkBuilder, $invoiceNumber, array $customsInfo, array $packageInfo, $recipientCountryId)
    {
        if ($customsInfo === []) {
            return;
        }

        foreach ($packageInfo as $packageId => $package) {
            // Validate required package structure
            if (!isset($package['params'])) {
                throw new \InvalidArgumentException('Package params missing for customs declaration');
            }

            $packageParams = $package['params'];

            // Validate required customs fields
            if (!isset($packageParams['content_type'])) {
                throw new \InvalidArgumentException('Content type required for international shipments');
            }

            // Set customs details once per package
            $sdkBuilder->setCustomsDetails(
                $packageParams['content_type'],
                $customsInfo['place_of_commital'] ?? '',
                $customsInfo['additional_fee'] ?? 0.0,
                $packageParams['content_type_other'] ?? '',
                $customsInfo['terms_of_trade'] ?? 'DDP',
                $invoiceNumber,
                $customsInfo['permit_number'] ?? '',
                $customsInfo['attestation_number'] ?? '',
                isset($customsInfo['export_notification']) && (bool) $customsInfo['export_notification'],
                null,  // sendersCustomsReference (not currently used)
                null,  // addresseesCustomsReference (not currently used)
                $customsInfo['master_reference_number'] ?? null,  // MRN
            );

            // Validate items exist
            if (!isset($package['items']) || $package['items'] === []) {
                throw new \InvalidArgumentException('Items required for customs declaration');
            }

            // Add each item as export item
            foreach ($package['items'] as $itemId => $item) {
                // Validate required item fields
                if (!isset($item['qty'])) {
                    throw new \InvalidArgumentException("Item quantity missing for item $itemId");
                }
                if (!isset($item['weight'])) {
                    throw new \InvalidArgumentException("Item weight missing for item $itemId");
                }
                if (!isset($item['customs_value'])) {
                    throw new \InvalidArgumentException("Item customs value missing for item $itemId");
                }
                if (!isset($item['customs'])) {
                    throw new \InvalidArgumentException("Item customs data missing for item $itemId");
                }

                $itemCustoms = $item['customs'];

                // Validate required customs fields
                if (!isset($itemCustoms['description']) || $itemCustoms['description'] === '') {
                    throw new \InvalidArgumentException("Item description required for customs declaration (item $itemId)");
                }
                if (!isset($itemCustoms['tariff_number']) || $itemCustoms['tariff_number'] === '') {
                    throw new \InvalidArgumentException("Item tariff number required for customs declaration (item $itemId)");
                }

                // Validate HS code (tariff number) length
                $tariffNumber = $itemCustoms['tariff_number'];
                if (in_array($recipientCountryId, Dhl_Versenden_Model_Util_UsCustomsTerritory::COUNTRY_CODES, true)) {
                    // US CBP requires a 10-digit HTSUS code per item for USA/Puerto Rico.
                    // The DHL API accepts shorter codes without error, but such shipments
                    // risk import rejection in the US — block label creation here instead.
                    if (!preg_match('/^\d{10}$/', $tariffNumber)) {
                        throw new \InvalidArgumentException(
                            "Shipments to the USA and Puerto Rico require a 10-digit HTSUS customs tariff number for every item (item $itemId)"
                        );
                    }
                } else {
                    // API requires 6-11 digits for all other destinations
                    $tariffLength = strlen($tariffNumber);
                    if ($tariffLength < 6 || $tariffLength > 11) {
                        throw new \InvalidArgumentException("HS code must be between 6 and 11 digits (item $itemId)");
                    }
                }

                if (!isset($itemCustoms['country_of_origin']) || $itemCustoms['country_of_origin'] === '') {
                    throw new \InvalidArgumentException("Item country of origin required for customs declaration (item $itemId)");
                }

                // Convert country code from ISO-2 to ISO-3 for REST API
                $countryOfOrigin = $itemCustoms['country_of_origin'];
                if (strlen($countryOfOrigin) === 2) {
                    $countryDirectory = Mage::getModel('directory/country')->loadByCode($countryOfOrigin);
                    $countryOfOrigin = $countryDirectory->getIso3Code();
                }

                $weightInKG = $item['weight'];
                if ($this->_unitOfMeasure === 'G') {
                    $weightInKG *= 0.001;
                }

                $weightInKG = max($weightInKG, $this->_minWeightInKG);

                $sdkBuilder->addExportItem(
                    $item['qty'],
                    $itemCustoms['description'],
                    $item['customs_value'],
                    $weightInKG,
                    $itemCustoms['tariff_number'],
                    $countryOfOrigin,
                );
            }
        }
    }

}
