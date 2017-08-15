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
 * Encapsulates autoloader registration in constructor for arbitrary observers
 */
abstract class Dhl_Versenden_Model_Observer_AbstractObserver
{
    use Dhl_Versenden_Model_Trait_Autoloader;

    /**
     * Dhl_Versenden_Model_Observer_AbstractObserver constructor.
     *
     * Initialize registerAutoload for events not going through controller_front_init_before event
     */
    public function __construct()
    {
        $this->registerAutoload();
    }
}