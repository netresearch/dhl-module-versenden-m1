<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Model_Observer_Autoload
{
    /**
     * Register autoloader when frontend interaction is involved.
     * - event: controller_front_init_before
     *
     * This event is not triggered when module code is run from command line:
     * - shipping module cron task
     * - 3rd party cron tasks
     *
     * @see \Dhl_Versenden_Model_Cron::__construct
     * @see \Dhl_Versenden_Model_Observer_AbstractObserver::__construct
     */
    public function registerAutoload()
    {
        Mage::getSingleton('dhl_versenden/autoloader')->registerAutoload();
    }
}
