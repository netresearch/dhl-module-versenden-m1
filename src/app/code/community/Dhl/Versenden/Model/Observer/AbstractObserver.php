<?php

/**
 * See LICENSE.md for license details.
 */

abstract class Dhl_Versenden_Model_Observer_AbstractObserver
{
    /**
     * Dhl_Versenden_Model_Observer_AbstractObserver constructor.
     *
     * Observer methods may get triggered through 3rd party cron tasks. To make
     * sure the library classes are loaded in those cases, we need to register
     * the autoloader before the observer method is invoked.
     */
    public function __construct()
    {
        Mage::getSingleton('dhl_versenden/autoloader')->registerAutoload();
    }
}
