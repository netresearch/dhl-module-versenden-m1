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
 * @author    Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */

/**
 * Dhl_Versenden_Test_Helper_AddressTest
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Test_Helper_AddressTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @param string $street
     *
     * @test
     * @loadExpectation
     * @dataProvider dataProvider
     */
    public function splitStreet($street)
    {
        /** @var Dhl_Versenden_Helper_Address $helper */
        $helper   = Mage::helper('dhl_versenden/address');
        $split    = $helper->splitStreet($street);
        $expected = $this->expected('auto')->getData();

        $this->assertEquals($expected, $split);
    }
}
