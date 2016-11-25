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
 * @package   Dhl\Versenden\Bcs\Api\Webservice\ResponseData
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */
namespace Dhl\Versenden\Bcs\Api\Webservice\ResponseData\CreateShipment;
use Dhl\Versenden\Bcs\Api\Pdf\Adapter;
use Dhl\Versenden\Bcs\Api\Webservice\ResponseData\Status\Item as CreationState;

/**
 * Label
 *
 * @category Dhl
 * @package  Dhl\Versenden\Bcs\Api\Webservice\ResponseData
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Label
{
    /** @var CreationState */
    private $status;
    /** @var string */
    private $sequenceNumber;
    /** @var string */
    private $label;
    /** @var string */
    private $returnLabel;
    /** @var string */
    private $exportLabel;
    /** @var string */
    private $codLabel;

    /**
     * Label constructor.
     * @param CreationState $status
     * @param string $sequenceNumber
     * @param string $label
     * @param string $returnLabel
     * @param string $exportLabel
     * @param string $codLabel
     */
    public function __construct(CreationState $status, $sequenceNumber, $label,
                                $returnLabel = null, $exportLabel = null, $codLabel = null
    ) {
        $this->status = $status;
        $this->sequenceNumber = $sequenceNumber;
        $this->label = $label;
        $this->returnLabel = $returnLabel;
        $this->exportLabel = $exportLabel;
        $this->codLabel = $codLabel;
    }

    /**
     * @return CreationState
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getSequenceNumber()
    {
        return $this->sequenceNumber;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return string
     */
    public function getReturnLabel()
    {
        return $this->returnLabel;
    }

    /**
     * @return string
     */
    public function getExportLabel()
    {
        return $this->exportLabel;
    }

    /**
     * @return string
     */
    public function getCodLabel()
    {
        return $this->codLabel;
    }

    public function isCreated()
    {
        return ($this->getStatus()->getStatusCode() === '0');
    }

    /**
     * @param Adapter $pdfLib
     * @return string
     */
    public function getAllLabels(Adapter $pdfLib)
    {
        return $pdfLib->merge([
            $this->getLabel(),
            $this->getReturnLabel(),
            $this->getCodLabel(),
            $this->getExportLabel(),
        ]);
    }
}
