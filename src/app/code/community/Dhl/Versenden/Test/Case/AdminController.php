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
 * Dhl_Versenden_Test_Case_AdminController
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Test_Case_AdminController
    extends EcomDev_PHPUnit_Test_Case_Controller
{
    /**
     * Tease EE a bit before running actual test: mock interfering observer.
     * @link http://www.schmengler-se.de/en/?p=688
     */
    protected function setUp()
    {
        parent::setUp();
        $this->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

        $adminObserverMock = $this->getModelMock(
            'enterprise_admingws/observer',
            array('adminControllerPredispatch')
        );

        $adminObserverMock
            ->expects($this->any())
            ->method('adminControllerPredispatch')
            ->will($this->returnSelf())
        ;
        $this->replaceByMock('singleton', 'enterprise_admingws/observer', $adminObserverMock);

        $this->mockAdminUserSession();
    }
}
