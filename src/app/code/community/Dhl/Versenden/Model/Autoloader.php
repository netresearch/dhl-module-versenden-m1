<?php

/**
 * See LICENSE.md for license details.
 */


class Dhl_Versenden_Model_Autoloader
{
    /**
     * Order of autoloaders gets shuffled if the same autoloader is registered
     * more than once. Remember state to avoid this.
     *
     * @var bool
     */
    protected $_isRegistered = false;

    /**
     * Register autoloader in order to locate the extension libraries.
     *
     * To make sure the autoloader gets registered only once, use registration
     * with registered check.
     * @see registerAutoload()
     */
    public static function register()
    {
        if (!Mage::getModel('dhl_versenden/config')->isAutoloadEnabled()) {
            return;
        }

        /** @var Dhl_Versenden_Helper_Autoloader $autoloader */
        $autoloader = Mage::helper('dhl_versenden/autoloader');
        $autoloader->addNamespace(
            'Psr\\', // prefix
            sprintf('%s/Dhl/Versenden/Psr/', Mage::getBaseDir('lib')),
        );
        $autoloader->addNamespace(
            'Dhl\\Versenden\\Cig\\', // prefix
            sprintf('%s/Dhl/Versenden/Cig/', Mage::getBaseDir('lib')),
        );
        $autoloader->addNamespace(
            'Dhl\\Versenden\\ParcelDe\\', // prefix
            sprintf('%s/Dhl/Versenden/ParcelDe/', Mage::getBaseDir('lib')),
        );

        $autoloader->register();
    }

    /**
     * Register autoloader with registered check.
     */
    public function registerAutoload()
    {
        if (!$this->_isRegistered) {
            static::register();
            $this->_isRegistered = true;
        }
    }
}
