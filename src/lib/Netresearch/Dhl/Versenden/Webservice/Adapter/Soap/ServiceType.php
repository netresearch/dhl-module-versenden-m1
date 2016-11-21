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
 * @package   Netresearch\Dhl\Versenden\Webservice\Soap
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */
namespace Netresearch\Dhl\Versenden\Webservice\Adapter\Soap;
use Netresearch\Dhl\Bcs\Api as VersendenApi;
use Netresearch\Dhl\Bcs\Api\Ident;
use Netresearch\Dhl\Bcs\Api\Serviceconfiguration;
use Netresearch\Dhl\Bcs\Api\ServiceconfigurationAdditionalInsurance;
use Netresearch\Dhl\Bcs\Api\ServiceconfigurationCashOnDelivery;
use Netresearch\Dhl\Bcs\Api\ServiceconfigurationDateOfDelivery;
use Netresearch\Dhl\Bcs\Api\ServiceconfigurationDeliveryTimeframe;
use Netresearch\Dhl\Bcs\Api\ServiceconfigurationDetails;
use Netresearch\Dhl\Bcs\Api\ServiceconfigurationEndorsement;
use Netresearch\Dhl\Bcs\Api\ServiceconfigurationIC;
use Netresearch\Dhl\Bcs\Api\ServiceconfigurationISR;
use Netresearch\Dhl\Bcs\Api\ServiceconfigurationShipmentHandling;
use Netresearch\Dhl\Bcs\Api\ServiceconfigurationVisualAgeCheck;
use Netresearch\Dhl\Versenden\Webservice\RequestData;

/**
 * ServiceType
 *
 * @category Dhl
 * @package  Netresearch\Dhl\Versenden\Webservice\Soap
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class ServiceType implements RequestType
{
    /**
     * @param RequestData\ShipmentOrder\ServiceSelection $requestData
     * @return VersendenApi\ShipmentService
     */
    public static function prepare(RequestData $requestData)
    {
        $service = new VersendenApi\ShipmentService();

        if ($requestData->getPreferredTime()) {
            $ptConfig = new ServiceconfigurationDeliveryTimeframe(true, $requestData->getPreferredTime());
            $service->setPreferredTime($ptConfig);
        }

        if ($individualSenderRequirement = false) {
            // free text
            $isrConfig = new ServiceconfigurationISR(false, '');
            $service->setIndividualSenderRequirement($isrConfig);
        }

        if ($packagingReturn = false) {
            $prConfig = new Serviceconfiguration(false);
            $service->setPackagingReturn($prConfig);
        }

        if ($noticeOfNonDelivery = false) {
            $nondConfig = new Serviceconfiguration(false);
            $service->setNoticeOfNonDeliverability($nondConfig);
        }

        if ($shipmentHandling = false) {
            // possible types: a, b, c, d, e
            $shConfig = new ServiceconfigurationShipmentHandling(false, '');
            $service->setShipmentHandling($shConfig);
        }

        if ($endorsement = false) {
            // possible types: SOZU, ZWZU, IMMEDIATE, AFTER_DEADLINE, ABANDONMENT
            $eConfig = new ServiceconfigurationEndorsement(false, '');
            $service->setEndorsement($eConfig);
        }

        if ($requestData->getVisualCheckOfAge()) {
            $vcaConfig = new ServiceconfigurationVisualAgeCheck(
                true,
                $requestData->getVisualCheckOfAge()
            );
            $service->setVisualCheckOfAge($vcaConfig);
        }

        if ($requestData->getPreferredLocation()) {
            $plConfig = new ServiceconfigurationDetails(
                true,
                $requestData->getPreferredLocation()
            );
            $service->setPreferredLocation($plConfig);
        }

        if ($requestData->getPreferredNeighbour()) {
            $pnConfig = new ServiceconfigurationDetails(
                true,
                $requestData->getPreferredNeighbour()
            );
            $service->setPreferredNeighbour($pnConfig);
        }

        if ($requestData->getPreferredDay()) {
            $pdConfig = new ServiceconfigurationDetails(true, $requestData->getPreferredDay());
            $service->setPreferredDay($pdConfig);
        }

        if ($perishables = false) {
            $pConfig = new Serviceconfiguration($perishables);
            $service->setPerishables($pConfig);
        }

        if ($personally = false) {
            $pConfig = new Serviceconfiguration($personally);
            $service->setPersonally($pConfig);
        }

        if ($noNeighbourDelivery = false) {
            $nndConfig = new Serviceconfiguration($noNeighbourDelivery);
            $service->setNoNeighbourDelivery($nndConfig);
        }

        if ($namedPersonOnly = false) {
            $npoConfig = new Serviceconfiguration($namedPersonOnly);
            $service->setNamedPersonOnly($npoConfig);
        }

        if ($returnReceipt = false) {
            $rrConfig = new Serviceconfiguration($returnReceipt);
            $service->setReturnReceipt($rrConfig);
        }

        if ($premium = false) {
            $pConfig = new Serviceconfiguration($premium);
            $service->setPremium($pConfig);
        }

        if ($requestData->getCod()) {
            $codAmount = $requestData->getCod();
            $codConfig = new ServiceconfigurationCashOnDelivery(true, true, $codAmount);
            $service->setCashOnDelivery($codConfig);
        }

        if ($requestData->getInsurance()) {
            $insuranceAmount = $requestData->getInsurance();
            $iConfig = new ServiceconfigurationAdditionalInsurance(true, $insuranceAmount);
            $service->setAdditionalInsurance($iConfig);
        }

        if ($requestData->isBulkyGoods()) {
            $bgConfig = new Serviceconfiguration(true);
            $service->setBulkyGoods($bgConfig);
        }

        if ($identCheck = false) {
            $ident = new Ident('surname', 'givenName', 'dateOfBirth', 'minimumAge');
            $icConfig = new ServiceconfigurationIC($ident, true);
            $service->setIdentCheck($icConfig);
        }

        return $service;
    }
}
