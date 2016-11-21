<?php

namespace Netresearch\Dhl\Bcs\Api;

class ExportDocumentType
{

    /**
     * @var invoiceNumber $invoiceNumber
     */
    protected $invoiceNumber = null;

    /**
     * @var exportType $exportType
     */
    protected $exportType = null;

    /**
     * @var exportTypeDescription $exportTypeDescription
     */
    protected $exportTypeDescription = null;

    /**
     * @var termsOfTrade $termsOfTrade
     */
    protected $termsOfTrade = null;

    /**
     * @var placeOfCommital $placeOfCommital
     */
    protected $placeOfCommital = null;

    /**
     * @var additionalFee $additionalFee
     */
    protected $additionalFee = null;

    /**
     * @var permitNumber $permitNumber
     */
    protected $permitNumber = null;

    /**
     * @var attestationNumber $attestationNumber
     */
    protected $attestationNumber = null;

    /**
     * @var Serviceconfiguration $WithElectronicExportNtfctn
     */
    protected $WithElectronicExportNtfctn = null;

    /**
     * @var ExportDocPosition $ExportDocPosition
     */
    protected $ExportDocPosition = null;

    /**
     * @param exportType $exportType
     * @param placeOfCommital $placeOfCommital
     * @param additionalFee $additionalFee
     */
    public function __construct($exportType, $placeOfCommital, $additionalFee)
    {
      $this->exportType = $exportType;
      $this->placeOfCommital = $placeOfCommital;
      $this->additionalFee = $additionalFee;
    }

    /**
     * @return invoiceNumber
     */
    public function getInvoiceNumber()
    {
      return $this->invoiceNumber;
    }

    /**
     * @param invoiceNumber $invoiceNumber
     * @return \Netresearch\Dhl\Bcs\Api\ExportDocumentType
     */
    public function setInvoiceNumber($invoiceNumber)
    {
      $this->invoiceNumber = $invoiceNumber;
      return $this;
    }

    /**
     * @return exportType
     */
    public function getExportType()
    {
      return $this->exportType;
    }

    /**
     * @param exportType $exportType
     * @return \Netresearch\Dhl\Bcs\Api\ExportDocumentType
     */
    public function setExportType($exportType)
    {
      $this->exportType = $exportType;
      return $this;
    }

    /**
     * @return exportTypeDescription
     */
    public function getExportTypeDescription()
    {
      return $this->exportTypeDescription;
    }

    /**
     * @param exportTypeDescription $exportTypeDescription
     * @return \Netresearch\Dhl\Bcs\Api\ExportDocumentType
     */
    public function setExportTypeDescription($exportTypeDescription)
    {
      $this->exportTypeDescription = $exportTypeDescription;
      return $this;
    }

    /**
     * @return termsOfTrade
     */
    public function getTermsOfTrade()
    {
      return $this->termsOfTrade;
    }

    /**
     * @param termsOfTrade $termsOfTrade
     * @return \Netresearch\Dhl\Bcs\Api\ExportDocumentType
     */
    public function setTermsOfTrade($termsOfTrade)
    {
      $this->termsOfTrade = $termsOfTrade;
      return $this;
    }

    /**
     * @return placeOfCommital
     */
    public function getPlaceOfCommital()
    {
      return $this->placeOfCommital;
    }

    /**
     * @param placeOfCommital $placeOfCommital
     * @return \Netresearch\Dhl\Bcs\Api\ExportDocumentType
     */
    public function setPlaceOfCommital($placeOfCommital)
    {
      $this->placeOfCommital = $placeOfCommital;
      return $this;
    }

    /**
     * @return additionalFee
     */
    public function getAdditionalFee()
    {
      return $this->additionalFee;
    }

    /**
     * @param additionalFee $additionalFee
     * @return \Netresearch\Dhl\Bcs\Api\ExportDocumentType
     */
    public function setAdditionalFee($additionalFee)
    {
      $this->additionalFee = $additionalFee;
      return $this;
    }

    /**
     * @return permitNumber
     */
    public function getPermitNumber()
    {
      return $this->permitNumber;
    }

    /**
     * @param permitNumber $permitNumber
     * @return \Netresearch\Dhl\Bcs\Api\ExportDocumentType
     */
    public function setPermitNumber($permitNumber)
    {
      $this->permitNumber = $permitNumber;
      return $this;
    }

    /**
     * @return attestationNumber
     */
    public function getAttestationNumber()
    {
      return $this->attestationNumber;
    }

    /**
     * @param attestationNumber $attestationNumber
     * @return \Netresearch\Dhl\Bcs\Api\ExportDocumentType
     */
    public function setAttestationNumber($attestationNumber)
    {
      $this->attestationNumber = $attestationNumber;
      return $this;
    }

    /**
     * @return Serviceconfiguration
     */
    public function getWithElectronicExportNtfctn()
    {
      return $this->WithElectronicExportNtfctn;
    }

    /**
     * @param Serviceconfiguration $WithElectronicExportNtfctn
     * @return \Netresearch\Dhl\Bcs\Api\ExportDocumentType
     */
    public function setWithElectronicExportNtfctn($WithElectronicExportNtfctn)
    {
      $this->WithElectronicExportNtfctn = $WithElectronicExportNtfctn;
      return $this;
    }

    /**
     * @return ExportDocPosition
     */
    public function getExportDocPosition()
    {
      return $this->ExportDocPosition;
    }

    /**
     * @param ExportDocPosition $ExportDocPosition
     * @return \Netresearch\Dhl\Bcs\Api\ExportDocumentType
     */
    public function setExportDocPosition($ExportDocPosition)
    {
      $this->ExportDocPosition = $ExportDocPosition;
      return $this;
    }

}
