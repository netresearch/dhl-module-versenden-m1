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

/**
 * Class AbstractObserver
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
