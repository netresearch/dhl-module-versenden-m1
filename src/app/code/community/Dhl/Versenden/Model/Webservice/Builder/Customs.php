<?php

/**
 * See LICENSE.md for license details.
 */

use \Dhl\Versenden\Bcs\Api\Webservice\RequestData\ShipmentOrder\Export;

class Dhl_Versenden_Model_Webservice_Builder_Customs
{

    /** @var string */
    protected $_unitOfMeasure;

    /** @var float */
    protected $_minWeightInKG;

    /**
     * Dhl_Versenden_Model_Webservice_Builder_Package constructor.
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
     * @param string $invoiceNumber
     * @param string[] $customsInfo
     * @param string[] $packageInfo
     * @return Export\DocumentCollection
     */
    public function getExportDocuments($invoiceNumber, array $customsInfo, array $packageInfo)
    {
        $documentCollection = new Export\DocumentCollection();

        if (empty($customsInfo)) {
            return $documentCollection;
        }

        foreach ($packageInfo as $packageId => $package) {
            $exportPositions = new Export\PositionCollection();

            foreach ($package['items'] as $itemId => $item) {
                $weightInKG = $item['weight'];
                if ($this->_unitOfMeasure == 'G') {
                    $weightInKG *= 0.001;
                }

                $weightInKG = max($weightInKG, $this->_minWeightInKG);
                $position = new Export\Position(
                    $itemId,
                    $item['customs']['description'],
                    $item['customs']['country_of_origin'],
                    $item['customs']['tariff_number'],
                    $item['qty'],
                    $weightInKG,
                    $item['customs_value']
                );
                $exportPositions->addItem($position);
            }

            $document = new Export\Document(
                $packageId,
                $invoiceNumber,
                $package['params']['content_type'],
                $package['params']['content_type_other'],
                $customsInfo['terms_of_trade'],
                $customsInfo['additional_fee'],
                $customsInfo['place_of_commital'],
                $customsInfo['permit_number'],
                $customsInfo['attestation_number'],
                isset($customsInfo['export_notification']) && $customsInfo['export_notification'],
                $exportPositions
            );

            $documentCollection->addItem($document);
        }

        return $documentCollection;
    }
}
