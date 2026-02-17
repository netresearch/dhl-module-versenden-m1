<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\ParcelDe\Info;

class Services extends ArrayableInfo
{
    /**
     * @var bool false or true
     */
    public $parcelOutletRouting;

    /**
     * @var bool|string false or date
     */
    public $preferredDay;

    /**
     * @var bool|string false or location
     */
    public $preferredLocation;

    /**
     * @var bool|string false or neighbour address
     */
    public $preferredNeighbour;

    /**
     * @var bool false or true
     */
    public $parcelAnnouncement;

    /**
     * @var bool|string false or A16 or A18
     */
    public $visualCheckOfAge;

    /**
     * @var bool false or true
     */
    public $returnShipment;

    /**
     * @var bool|float false or amount
     */
    public $insurance;

    /**
     * @var bool false or true
     */
    public $bulkyGoods;

    /**
     * @var bool|float false or amount
     */
    public $cod;

    /**
     * @var bool false or true
     */
    public $printOnlyIfCodeable;

    /**
     * @var bool false or true
     */
    public $noNeighbourDelivery;

    /**
     * @var bool false or true
     */
    public $goGreen;

    /**
     * @var bool|float false or amount
     */
    public $additionalInsurance;

    /**
     * @var bool false or true
     */
    public $closestDropPoint;

    /**
     * @var bool|string false or location type
     */
    public $deliveryLocation;

    /**
     * @var bool|string false or delivery type
     */
    public $deliveryType;

    /**
     * @var bool|string false or endorsement type
     */
    public $endorsement;

    /**
     * @var bool false or true
     */
    public $namedPersonOnly;

    /**
     * @var bool false or true
     */
    public $postalDeliveryDutyPaid;

    /**
     * @var bool false or true
     */
    public $signedForByRecipient;
}
