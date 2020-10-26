<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Model_Resource_Label_Status extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * primary key is foreign key to order table.
     *
     * @var bool
     */
    protected $_isPkAutoIncrement = false;

    /**
     * Resource initialization.
     */
    public function _construct()
    {
        $this->_init('dhl_versenden/label_status', 'order_id');
    }
}
