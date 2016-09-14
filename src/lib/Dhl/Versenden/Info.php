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

/**
 * Info
 *
 * @category Dhl
 * @package  Dhl\Versenden\Info
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Info extends Info\AbstractInfo
{
    const SCHEMA_VERSION = '1.0';

    /** @var string */
    public $schemaVersion;
    /** @var Info\Receiver */
    public $receiver;
    /** @var Info\Services */
    public $services;
    /** @var Info\ExportData */
    public $exportData;

    /**
     * Info constructor.
     */
    public function __construct()
    {
        $this->schemaVersion = self::SCHEMA_VERSION;
        $this->receiver = new Info\Receiver();
        $this->services = new Info\Services();
        $this->exportData = new Info\ExportData();
    }

    /**
     * @return Info\Receiver
     */
    public function getReceiver()
    {
        return $this->receiver;
    }

    /**
     * @return Info\Services
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * @return Info\ExportData
     */
    public function getExportData()
    {
        return $this->exportData;
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
            $info->receiver = Info\Receiver::fromObject($object->receiver);
        }
        if (isset($object->services)) {
            $info->services = Info\Services::fromObject($object->services);
        }
        if (isset($object->exportData)) {
            $info->exportData = Info\ExportData::fromObject($object->exportData);
        }

        return $info;
    }
}
