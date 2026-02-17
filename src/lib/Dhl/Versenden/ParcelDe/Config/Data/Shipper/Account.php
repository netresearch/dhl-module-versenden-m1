<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\ParcelDe\Config\Data\Shipper;

use Dhl\Versenden\ParcelDe\Config\ConfigData;

class Account extends ConfigData
{
    /** @var string */
    private $user;
    /** @var string */
    private $signature;
    /** @var string */
    private $ekp;
    /** @var string[] */
    private $participations;

    /**
     * Account constructor.
     * @param string $user
     * @param string $signature
     * @param string $ekp
     * @param string[] $participations
     */
    public function __construct($user, $signature, $ekp, $participations)
    {
        $this->validateLength('EKP', $ekp, 10, 10);
        foreach ($participations as $procedure => $participation) {
            $this->validateLength('Participation', $participation, 2, 2);
        }

        $this->user = $user;
        $this->signature = $signature;
        $this->ekp = $ekp;
        $this->participations = $participations;
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * @return string
     */
    public function getEkp()
    {
        return $this->ekp;
    }

    /**
     * @return string[]
     */
    public function getParticipations()
    {
        return $this->participations;
    }

    /**
     * @param string $procedure
     * @return null|string
     */
    public function getParticipation($procedure)
    {
        if (!isset($this->participations[$procedure])) {
            return null;
        }
        return $this->participations[$procedure];
    }
}
