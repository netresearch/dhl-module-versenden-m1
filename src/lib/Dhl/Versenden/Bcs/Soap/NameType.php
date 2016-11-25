<?php

namespace Dhl\Versenden\Bcs\Soap;

class NameType
{

    /**
     * @var name1 $name1
     */
    protected $name1 = null;

    /**
     * @var name2 $name2
     */
    protected $name2 = null;

    /**
     * @var name3 $name3
     */
    protected $name3 = null;

    /**
     * @param name1 $name1
     * @param name2 $name2
     * @param name3 $name3
     */
    public function __construct($name1, $name2, $name3)
    {
      $this->name1 = $name1;
      $this->name2 = $name2;
      $this->name3 = $name3;
    }

    /**
     * @return name1
     */
    public function getName1()
    {
      return $this->name1;
    }

    /**
     * @param name1 $name1
     * @return \Dhl\Versenden\Bcs\Soap\NameType
     */
    public function setName1($name1)
    {
      $this->name1 = $name1;
      return $this;
    }

    /**
     * @return name2
     */
    public function getName2()
    {
      return $this->name2;
    }

    /**
     * @param name2 $name2
     * @return \Dhl\Versenden\Bcs\Soap\NameType
     */
    public function setName2($name2)
    {
      $this->name2 = $name2;
      return $this;
    }

    /**
     * @return name3
     */
    public function getName3()
    {
      return $this->name3;
    }

    /**
     * @param name3 $name3
     * @return \Dhl\Versenden\Bcs\Soap\NameType
     */
    public function setName3($name3)
    {
      $this->name3 = $name3;
      return $this;
    }

}
