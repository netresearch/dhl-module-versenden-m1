<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Test_Helper_ServiceTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     */
    public function getServiceDetailsValidationCondition()
    {
        $helper = Mage::helper('dhl_versenden/service');
        static::assertEquals(
            Dhl_Versenden_Helper_Service::PREFERRED_SERVICE_ANY_ENABLED,
            $helper->getServiceDetailsValidationCondition(),
        );
    }

    /**
     * @test
     */
    public function getInputSpecialCharsValidationCondition()
    {
        $helper = Mage::helper('dhl_versenden/service');
        static::assertEquals(
            Dhl_Versenden_Helper_Service::PREFERRED_SERVICE_ANY_ENABLED,
            $helper->getInputSpecialCharsValidationCondition(),
        );
    }

    /**
     * @test
     */
    public function getServiceCombinationValidationCondition()
    {
        $helper = Mage::helper('dhl_versenden/service');
        static::assertEquals(
            Dhl_Versenden_Helper_Service::PREFERRED_SERVICE_ALL_ENABLED,
            $helper->getServiceCombinationValidationCondition(),
        );
    }

    /**
     * @test
     * @loadFixture Helper_ServiceTest
     */
    public function isLocationOrNeighbourEnabled()
    {
        $helper = Mage::helper('dhl_versenden/service');

        // Both enabled - should return true
        $this->setCurrentStore('store_both_enabled');
        static::assertTrue($helper->isLocationOrNeighbourEnabled(), 'Both enabled should return true');

        // Only location enabled - should return true
        $this->setCurrentStore('store_location_only');
        static::assertTrue($helper->isLocationOrNeighbourEnabled(), 'Only location should return true');

        // Only neighbour enabled - should return true
        $this->setCurrentStore('store_neighbour_only');
        static::assertTrue($helper->isLocationOrNeighbourEnabled(), 'Only neighbour should return true');

        // Neither enabled - should return false
        $this->setCurrentStore('store_neither');
        static::assertFalse($helper->isLocationOrNeighbourEnabled(), 'Neither enabled should return false');
    }

    /**
     * @test
     * @loadFixture Helper_ServiceTest
     */
    public function isLocationAndNeighbourEnabled()
    {
        $helper = Mage::helper('dhl_versenden/service');

        // Both enabled - should return true
        $this->setCurrentStore('store_both_enabled');
        static::assertTrue($helper->isLocationAndNeighbourEnabled(), 'Both enabled should return true');

        // Only location enabled - should return false
        $this->setCurrentStore('store_location_only');
        static::assertFalse($helper->isLocationAndNeighbourEnabled(), 'Only location should return false');

        // Only neighbour enabled - should return false
        $this->setCurrentStore('store_neighbour_only');
        static::assertFalse($helper->isLocationAndNeighbourEnabled(), 'Only neighbour should return false');

        // Neither enabled - should return false
        $this->setCurrentStore('store_neither');
        static::assertFalse($helper->isLocationAndNeighbourEnabled(), 'Neither enabled should return false');
    }
}
