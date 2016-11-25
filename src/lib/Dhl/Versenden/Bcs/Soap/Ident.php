<?php

namespace Dhl\Versenden\Bcs\Soap;

class Ident
{

    /**
     * @var surname $surname
     */
    protected $surname = null;

    /**
     * @var givenName $givenName
     */
    protected $givenName = null;

    /**
     * @var dateOfBirth $dateOfBirth
     */
    protected $dateOfBirth = null;

    /**
     * @var minimumAge $minimumAge
     */
    protected $minimumAge = null;

    /**
     * @param surname $surname
     * @param givenName $givenName
     * @param dateOfBirth $dateOfBirth
     * @param minimumAge $minimumAge
     */
    public function __construct($surname, $givenName, $dateOfBirth, $minimumAge)
    {
      $this->surname = $surname;
      $this->givenName = $givenName;
      $this->dateOfBirth = $dateOfBirth;
      $this->minimumAge = $minimumAge;
    }

    /**
     * @return surname
     */
    public function getSurname()
    {
      return $this->surname;
    }

    /**
     * @param surname $surname
     * @return \Dhl\Versenden\Bcs\Soap\Ident
     */
    public function setSurname($surname)
    {
      $this->surname = $surname;
      return $this;
    }

    /**
     * @return givenName
     */
    public function getGivenName()
    {
      return $this->givenName;
    }

    /**
     * @param givenName $givenName
     * @return \Dhl\Versenden\Bcs\Soap\Ident
     */
    public function setGivenName($givenName)
    {
      $this->givenName = $givenName;
      return $this;
    }

    /**
     * @return dateOfBirth
     */
    public function getDateOfBirth()
    {
      return $this->dateOfBirth;
    }

    /**
     * @param dateOfBirth $dateOfBirth
     * @return \Dhl\Versenden\Bcs\Soap\Ident
     */
    public function setDateOfBirth($dateOfBirth)
    {
      $this->dateOfBirth = $dateOfBirth;
      return $this;
    }

    /**
     * @return minimumAge
     */
    public function getMinimumAge()
    {
      return $this->minimumAge;
    }

    /**
     * @param minimumAge $minimumAge
     * @return \Dhl\Versenden\Bcs\Soap\Ident
     */
    public function setMinimumAge($minimumAge)
    {
      $this->minimumAge = $minimumAge;
      return $this;
    }

}
