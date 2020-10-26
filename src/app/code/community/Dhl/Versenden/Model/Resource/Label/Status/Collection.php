<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Model_Resource_Label_Status_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Collection initialization
     */
    public function _construct()
    {
        $this->_init('dhl_versenden/label_status');
    }

    /**
     * Save all the entities in the collection, wrap in transaction
     *
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract
     */
    public function save()
    {
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        $connection->beginTransaction();

        $result = parent::save();

        $connection->commit();

        return $result;
    }
}
