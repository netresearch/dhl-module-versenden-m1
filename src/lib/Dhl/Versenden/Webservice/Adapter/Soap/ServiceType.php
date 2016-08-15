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
 * @package   Dhl\Versenden\Webservice\Soap
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */
namespace Dhl\Versenden\Webservice\Adapter\Soap;
use Dhl\Bcs\Api as VersendenApi;
use Dhl\Bcs\Api\Ident;
use Dhl\Bcs\Api\Serviceconfiguration;
use Dhl\Bcs\Api\ServiceconfigurationAdditionalInsurance;
use Dhl\Bcs\Api\ServiceconfigurationCashOnDelivery;
use Dhl\Bcs\Api\ServiceconfigurationDateOfDelivery;
use Dhl\Bcs\Api\ServiceconfigurationDeliveryTimeframe;
use Dhl\Bcs\Api\ServiceconfigurationDetails;
use Dhl\Bcs\Api\ServiceconfigurationEndorsement;
use Dhl\Bcs\Api\ServiceconfigurationIC;
use Dhl\Bcs\Api\ServiceconfigurationISR;
use Dhl\Bcs\Api\ServiceconfigurationShipmentHandling;
use Dhl\Bcs\Api\ServiceconfigurationVisualAgeCheck;
use Dhl\Versenden\Webservice\RequestData;

/**
 * ServiceType
 *
 * @category Dhl
 * @package  Dhl\Versenden\Webservice\Soap
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

        if ($requestData->getDayOfDelivery()) {
            $dodConfig = new ServiceconfigurationDateOfDelivery(
                true,
                $requestData->getDayOfDelivery()
            );
            $service->setDayOfDelivery($dodConfig);
        }

        if ($requestData->getDeliveryTimeFrame()) {
            $dtfConfig = new ServiceconfigurationDeliveryTimeframe(
                true,
                $requestData->getDeliveryTimeFrame()
            );
            $service->setDeliveryTimeframe($dtfConfig);
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

        if ($preferredDay = false) {
            $pdConfig = new ServiceconfigurationDetails(true, '');
            $service->setPreferredDay($pdConfig);
        }

        if ($goGreen = false) {
            //TODO(nr): handle GoGreen
            $ggConfig = new Serviceconfiguration($goGreen);
            $service->setGoGreen($ggConfig);
        }

        if ($perishables = false) {
            $pConfig = new Serviceconfiguration($perishables);
            $service->setPerishables($pConfig);
        }

        if ($personally = false) {
            //TODO(nr): handle Personally?
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

        if ($cod = false) {
            //TODO(nr): handle COD
            $amount = 77;
            $codConfig = new ServiceconfigurationCashOnDelivery($cod, true, $amount);
            $service->setCashOnDelivery($codConfig);
        }

        if ($requestData->getInsurance()) {
            $iConfig = new ServiceconfigurationAdditionalInsurance(
                true,
                $requestData->getInsurance()
            );
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
