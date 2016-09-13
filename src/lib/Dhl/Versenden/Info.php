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
 * @package   Dhl\Versenden\Info
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */
namespace Dhl\Versenden;
use Dhl\Versenden\Info as DhlVersendenInfo;

/**
 * Info
 *
 * @category Dhl
 * @package  Dhl\Versenden\Info
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Info extends DhlVersendenInfo\AbstractInfo
{
    const SCHEMA_VERSION = '1.0';

    /** @var string */
    private $schemaVersion;
    /** @var DhlVersendenInfo\Receiver */
    private $receiver;
    /** @var DhlVersendenInfo\Packages */
    private $packages;
    /** @var DhlVersendenInfo\Services */
    private $services;
    /** @var DhlVersendenInfo\ExportData */
    private $exportData;

    /**
     * Info constructor.
     */
    public function __construct()
    {
        $this->schemaVersion = self::SCHEMA_VERSION;
    }

    /**
     * @return Info\Receiver
     */
    public function getReceiver()
    {
        return $this->receiver;
    }

    /**
     * @param Info\Receiver $receiver
     */
    public function setReceiver($receiver)
    {
        $this->receiver = $receiver;
    }

    /**
     * @return mixed
     */
    public function getPackages()
    {
        return $this->packages;
    }

    /**
     * @param mixed $packages
     */
    public function setPackages($packages)
    {
        $this->packages = $packages;
    }

    /**
     * @return mixed
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * @param mixed $services
     */
    public function setServices($services)
    {
        $this->services = $services;
    }

    /**
     * @return mixed
     */
    public function getExportData()
    {
        return $this->exportData;
    }

    /**
     * @param mixed $exportData
     */
    public function setExportData($exportData)
    {
        $this->exportData = $exportData;
    }

    /**
     * @param \stdClass $object
     * @return Info|null
     */
    public static function fromObject(\stdClass $object)
    {
        if (!isset($object->schemaVersion) || ($object->schemaVersion !== self::SCHEMA_VERSION)) {
            return null;
        }

        $info = new self();
        if (isset($object->receiver)) {
            $info->setReceiver(DhlVersendenInfo\Receiver::fromObject($object->receiver));
        }
        if (isset($object->packages)) {
            $info->setPackages(DhlVersendenInfo\Packages::fromObject($object->packages));
        }
        if (isset($object->services)) {
            $info->setServices(DhlVersendenInfo\Services::fromObject($object->services));
        }
        if (isset($object->exportData)) {
            $info->setExportData(DhlVersendenInfo\ExportData::fromObject($object->exportData));
        }

        return $info;
    }
}
