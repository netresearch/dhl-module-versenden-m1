<?php

namespace Dhl\Bcs\Api;

class PieceInformation
{

    /**
     * @var ShipmentNumberType $PieceNumber
     */
    protected $PieceNumber = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return ShipmentNumberType
     */
    public function getPieceNumber()
    {
      return $this->PieceNumber;
    }

    /**
     * @param ShipmentNumberType $PieceNumber
     * @return \Dhl\Bcs\Api\PieceInformation
     */
    public function setPieceNumber($PieceNumber)
    {
      $this->PieceNumber = $PieceNumber;
      return $this;
    }

}
