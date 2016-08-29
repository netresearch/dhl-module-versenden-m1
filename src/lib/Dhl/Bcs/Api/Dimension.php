<?php

namespace Dhl\Bcs\Api;

class Dimension
{

    /**
     * @var int $length
     */
    protected $length = null;

    /**
     * @var int $width
     */
    protected $width = null;

    /**
     * @var int $height
     */
    protected $height = null;

    /**
     * @var unit $unit
     */
    protected $unit = null;

    /**
     * @param int $length
     * @param int $width
     * @param int $height
     * @param unit $unit
     */
    public function __construct($length, $width, $height, $unit)
    {
      $this->length = $length;
      $this->width = $width;
      $this->height = $height;
      $this->unit = $unit;
    }

    /**
     * @return int
     */
    public function getLength()
    {
      return $this->length;
    }

    /**
     * @param int $length
     * @return \Dhl\Bcs\Api\Dimension
     */
    public function setLength($length)
    {
      $this->length = $length;
      return $this;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
      return $this->width;
    }

    /**
     * @param int $width
     * @return \Dhl\Bcs\Api\Dimension
     */
    public function setWidth($width)
    {
      $this->width = $width;
      return $this;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
      return $this->height;
    }

    /**
     * @param int $height
     * @return \Dhl\Bcs\Api\Dimension
     */
    public function setHeight($height)
    {
      $this->height = $height;
      return $this;
    }

    /**
     * @return unit
     */
    public function getUnit()
    {
      return $this->unit;
    }

    /**
     * @param unit $unit
     * @return \Dhl\Bcs\Api\Dimension
     */
    public function setUnit($unit)
    {
      $this->unit = $unit;
      return $this;
    }

}
