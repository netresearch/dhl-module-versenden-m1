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
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */

/**
 * Dhl_Versenden_Test_Model_Adminhtml_System_Config_Source_LoglevelTest
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Test_Model_Adminhtml_System_Config_Source_LoglevelTest
    extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     */
    public function sourceModel()
    {
        $sourceModel = Mage::getModel('dhl_versenden/adminhtml_system_config_source_loglevel');
        $optionArray = $sourceModel->toOptionArray();

        $this->assertInternalType('array', $optionArray);
        $this->assertNotEmpty($optionArray);

        foreach ($optionArray as $option) {
            $this->assertInternalType('array', $option);
            $this->assertArrayHasKey('value', $option);
            $this->assertArrayHasKey('label', $option);
            $this->assertInternalType('int', $option['value']);
            $this->assertLessThanOrEqual(Zend_Log::DEBUG, $option['value']);
        }
    }
}
