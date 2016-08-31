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
 * @package   Dhl_Versenden
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */
use \Dhl\Versenden\Webservice\RequestData\ShipmentOrder\Export;
/**
 * Dhl_Versenden_Model_Webservice_Builder_Service
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Model_Webservice_Builder_Customs
{
    /**
     * @param string $invoiceNumber
     * @param string[] $customsInfo
     * @param string[] $packageInfo
     * @return Export\Document
     */
    public function getExportDocument($invoiceNumber, array $customsInfo, array $packageInfo)
    {
        $collection = new Export\PositionCollection();

        foreach ($packageInfo as $packageId => $package) {
            foreach ($package['items'] as $item) {
                $itemCustomsInfo = $item['customs'];
                $position = new Export\Position(
                    $packageId,
                    $item['customs']['description'],
                    $item['customs']['country_of_origin'],
                    $item['customs']['tariff_number'],
                    $item['qty'],
                    $item['weight'],
                    $item['customs_value']
                );
                $collection->addItem($position);
            }
        }

        //TODO(nr): how to add one export document per parcel/package?
        reset($packageInfo);
        $package = current($packageInfo);

        return new Export\Document(
            $invoiceNumber,
            $package['params']['content_type'],
            $package['params']['content_type_other'],
            $customsInfo['terms_of_trade'],
            $customsInfo['additional_fee'],
            $customsInfo['place_of_commital'],
            $customsInfo['permit_number'],
            $customsInfo['attestation_number'],
            $customsInfo['export_notification'],
            $collection
        );
    }
}
