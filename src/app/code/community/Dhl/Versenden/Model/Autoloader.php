<?php
/**
 * Dhl Versenden
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to
 * newer versions in the future.
 *
 * @category  Dhl
 * @package   Dhl_Versenden
 * @author    Paul Siedler <paul.siedler@netresearch.de>
 * @copyright 2017 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
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
            "Psr\\", // prefix
            sprintf('%s/Dhl/Versenden/Psr/', Mage::getBaseDir('lib'))
        );
        $autoloader->addNamespace(
            "Dhl\\Versenden\\Bcs\\", // prefix
            sprintf('%s/Dhl/Versenden/Bcs/', Mage::getBaseDir('lib'))
        );
        $autoloader->addNamespace(
            "Dhl\\Versenden\\Cig\\", // prefix
            sprintf('%s/Dhl/Versenden/Cig/', Mage::getBaseDir('lib'))
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
