<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\Bcs\Api\Webservice\RequestData\ShipmentOrder\Export;

use Dhl\Versenden\Bcs\Api\Webservice\RequestData;

class Document extends RequestData
{
    /** @var int */
    private $packageId;
    /** @var string */
    private $invoiceNumber;
    /** @var string */
    private $exportType;
    /** @var string */
    private $exportTypeDescription;
    /** @var string */
    private $termsOfTrade;
    /** @var float */
    private $additionalFee;
    /** @var string */
    private $placeOfCommital;
    /** @var string */
    private $permitNumber;
    /** @var string */
    private $attestationNumber;
    /** @var boolean */
    private $electronicExportNotification;
    /** @var PositionCollection */
    private $positions;

    /**
     * Document constructor.
     * @param int $packageId
     * @param string $invoiceNumber
     * @param string $exportType
     * @param string $exportTypeDescription
     * @param string $termsOfTrade
     * @param float $additionalFee
     * @param string $placeOfCommital
     * @param string $permitNumber
     * @param string $attestationNumber
     * @param bool $electronicExportNotification
     * @param PositionCollection $positions
     */
    public function __construct(
        $packageId, $invoiceNumber, $exportType, $exportTypeDescription, $termsOfTrade,
        $additionalFee, $placeOfCommital, $permitNumber, $attestationNumber,
        $electronicExportNotification, PositionCollection $positions
    ) {
        $this->packageId = $packageId;
        $this->invoiceNumber = $invoiceNumber;
        $this->exportType = $exportType;
        $this->exportTypeDescription = $exportTypeDescription;
        $this->termsOfTrade = $termsOfTrade;
        $this->additionalFee = $additionalFee;
        $this->placeOfCommital = $placeOfCommital;
        $this->permitNumber = $permitNumber;
        $this->attestationNumber = $attestationNumber;
        $this->electronicExportNotification = $electronicExportNotification;
        $this->positions = $positions;
    }

    /**
     * @return int
     */
    public function getPackageId()
    {
        return $this->packageId;
    }

    /**
     * @return string
     */
    public function getInvoiceNumber()
    {
        return $this->invoiceNumber;
    }

    /**
     * @return string
     */
    public function getExportType()
    {
        return $this->exportType;
    }

    /**
     * @return string
     */
    public function getExportTypeDescription()
    {
        return $this->exportTypeDescription;
    }

    /**
     * @return string
     */
    public function getTermsOfTrade()
    {
        return $this->termsOfTrade;
    }

    /**
     * @return float
     */
    public function getAdditionalFee()
    {
        return $this->additionalFee;
    }

    /**
     * @return string
     */
    public function getPlaceOfCommital()
    {
        return $this->placeOfCommital;
    }

    /**
     * @return string
     */
    public function getPermitNumber()
    {
        return $this->permitNumber;
    }

    /**
     * @return string
     */
    public function getAttestationNumber()
    {
        return $this->attestationNumber;
    }

    /**
     * @return boolean
     */
    public function isElectronicExportNotification()
    {
        return $this->electronicExportNotification;
    }

    /**
     * @return PositionCollection
     */
    public function getPositions()
    {
        return $this->positions;
    }
}
