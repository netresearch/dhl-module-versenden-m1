<?php

/**
 * See LICENSE.md for license details.
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
