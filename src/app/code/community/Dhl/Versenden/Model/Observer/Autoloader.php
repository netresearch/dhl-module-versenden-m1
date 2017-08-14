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
 * PHP version 5
 *
 * @category  Dhl
 * @package   Dhl_Versenden
 * @author    Max Melzer <max.melzer@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */

/**
 * Dhl_Versenden_Model_Observer_Autoloader
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Model_Observer_Autoloader
{
    /**
     * Register autoloader in order to locate the extension libraries.
     */
    public function registerAutoload()
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

        $autoloader->register();
    }
}