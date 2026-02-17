<?php

/**
 * See LICENSE.md for license details.
 */


class Dhl_Versenden_Model_Webservice_Builder_Package
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
     * Build package details by populating the SDK builder.
     *
     * This method extracts weight and dimension information from packageInfo
     * and populates the SDK builder directly instead of creating SOAP DTOs.
     *
     * Note: Currently handles only the first package. Multi-package support
     * will be added when needed.
     *
     * @param \Dhl\Sdk\ParcelDe\Shipping\Api\ShipmentOrderRequestBuilderInterface $sdkBuilder
     * @param mixed[] $packageInfo
     * @return void
     */
    public function build(
        \Dhl\Sdk\ParcelDe\Shipping\Api\ShipmentOrderRequestBuilderInterface $sdkBuilder,
        array $packageInfo
    ) {
        // Extract first package details (multi-package support can be added later)
        $packageDetails = reset($packageInfo);

        // Initialize with default minimum weight (fallback for missing package data)
        // This ensures REST SDK always receives valid float, unlike SOAP's lenient typing
        $weightInKG = $this->_minWeightInKG;

        if ($packageDetails && isset($packageDetails['params'])) {
            $params = $packageDetails['params'];

            // Extract weight if provided
            if (isset($params['weight'])) {
                $weightUnit = $this->_unitOfMeasure;
                if (isset($params['weight_units']) && $params['weight_units']) {
                    $weightUnit = $params['weight_units'];
                }

                $weightInKG = $this->convertToKilograms($params['weight'], $weightUnit);
            }

            // Enforce minimum weight
            $weightInKG = max($weightInKG, $this->_minWeightInKG);

            // Set dimensions if all are provided
            $lengthInCM = isset($params['length']) && $params['length'] ? (int) $params['length'] : null;
            $widthInCM = isset($params['width']) && $params['width'] ? (int) $params['width'] : null;
            $heightInCM = isset($params['height']) && $params['height'] ? (int) $params['height'] : null;

            if ($lengthInCM !== null && $widthInCM !== null && $heightInCM !== null) {
                $sdkBuilder->setPackageDimensions($widthInCM, $lengthInCM, $heightInCM);
            }
        }

        // CRITICAL: Always set package weight, even with fallback default
        // REST SDK requires float type, cannot accept null (unlike SOAP)
        $sdkBuilder->setPackageDetails($weightInKG);
    }

    /**
     * Convert weight to kilograms from various units
     *
     * @param float $weight
     * @param string $unit Weight unit (G, KG, LBS, LB, OZ)
     * @return float Weight in kilograms
     */
    protected function convertToKilograms($weight, $unit)
    {
        $unit = strtoupper($unit);

        switch ($unit) {
            case 'G':
                return $weight * 0.001;
            case 'LBS':
            case 'LB':
                return $weight * 0.453592;
            case 'OZ':
                return $weight * 0.0283495;
            case 'KG':
            case 'KGS':
            case 'K':
            default:
                return $weight;
        }
    }
}
