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
use \Dhl\Versenden\Webservice\RequestData;
/**
 * Dhl_Versenden_Test_Model_Webservice_RequestData_CreateShipment_CreateShipmentTest
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Test_Model_Webservice_RequestData_CreateShipmentTest
    extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     * @dataProvider Dhl_Versenden_Test_Provider_ShipmentOrder::provider()
     *
     * @param RequestData\ShipmentOrder $shipmentOrder
     * @param Dhl_Versenden_Test_Expectation_ShipmentOrder $expectation
     */
    public function createShipment(
        RequestData\ShipmentOrder $shipmentOrder,
        Dhl_Versenden_Test_Expectation_ShipmentOrder $expectation
    ) {
        $major = '303';
        $minor = '808';
        $build = '909';

        $version = new RequestData\Version($major, $minor, $build);
        $collection = new RequestData\ShipmentOrderCollection();
        $collection->addItem($shipmentOrder);

        $createShipment = new RequestData\CreateShipment($version, $collection);


        $version = $createShipment->getVersion();
        $orders = $createShipment->getShipmentOrders()->getItems();

        $this->assertEquals($major, $version->getMajorRelease());
        $this->assertEquals($minor, $version->getMinorRelease());
        $this->assertEquals($build, $version->getBuild());

        $this->assertCount(1, $orders);
        /** @var RequestData\ShipmentOrder $order */
        $order = current($orders);

        $this->assertEquals(14, strlen($order->getAccountNumber()));

        $this->assertEquals(
            $expectation->getLabelResponseType(),
            $order->getLabelResponseType()
        );
        $this->assertEquals(
            $expectation->getShipmentSettingsProduct(),
            $order->getProductCode()
        );
        $this->assertEquals(
            $expectation->getSequenceNumber(),
            $order->getSequenceNumber()
        );

        $this->assertEquals(
            $expectation->getShipperAccountUser(),
            $order->getShipper()->getAccount()->getUser()
        );
        $this->assertEquals(
            $expectation->getShipperAccountSignature(),
            $order->getShipper()->getAccount()->getSignature()
        );
        $this->assertEquals(
            $expectation->getShipperAccountEkp(),
            $order->getShipper()->getAccount()->getEkp()
        );

        $productCode = $expectation->getShipmentSettingsProduct();
        $procedure = \Dhl\Versenden\Product::getProcedure($productCode);
        $this->assertEquals(
            $expectation->getShipperAccountParticipation($procedure),
            $order->getShipper()->getAccount()->getParticipation($procedure)
        );

        $this->assertEquals(
            $expectation->getShipperBankDataAccountOwner(),
            $order->getShipper()->getBankData()->getAccountOwner()
        );
        $this->assertEquals(
            $expectation->getShipperBankDataBankName(),
            $order->getShipper()->getBankData()->getBankName()
        );
        $this->assertEquals(
            $expectation->getShipperBankDataIban(),
            $order->getShipper()->getBankData()->getIban()
        );
        $this->assertEquals(
            $expectation->getShipperBankDataBic(),
            $order->getShipper()->getBankData()->getBic()
        );
        $this->assertEquals(
            $expectation->getShipperBankDataNote1(),
            $order->getShipper()->getBankData()->getNote1()
        );
        $this->assertEquals(
            $expectation->getShipperBankDataNote2(),
            $order->getShipper()->getBankData()->getNote2()
        );
        $this->assertEquals(
            $expectation->getShipperBankDataAccountReference(),
            $order->getShipper()->getBankData()->getAccountReference()
        );

        $this->assertEquals(
            $expectation->getShipperContactName1(),
            $order->getShipper()->getContact()->getName1()
        );
        $this->assertEquals(
            $expectation->getShipperContactName2(),
            $order->getShipper()->getContact()->getName2()
        );
        $this->assertEquals(
            $expectation->getShipperContactName3(),
            $order->getShipper()->getContact()->getName3()
        );
        $this->assertEquals(
            $expectation->getShipperContactStreetName(),
            $order->getShipper()->getContact()->getStreetName()
        );
        $this->assertEquals(
            $expectation->getShipperContactStreetNumber(),
            $order->getShipper()->getContact()->getStreetNumber()
        );
        $this->assertEquals(
            $expectation->getShipperContactAddressAddition(),
            $order->getShipper()->getContact()->getAddressAddition()
        );
        $this->assertEquals(
            $expectation->getShipperContactDispatchingInformation(),
            $order->getShipper()->getContact()->getDispatchingInformation()
        );
        $this->assertEquals(
            $expectation->getShipperContactZip(),
            $order->getShipper()->getContact()->getZip()
        );
        $this->assertEquals(
            $expectation->getShipperContactCity(),
            $order->getShipper()->getContact()->getCity()
        );
        $this->assertEquals(
            $expectation->getShipperContactCountry(),
            $order->getShipper()->getContact()->getCountry()
        );
        $this->assertEquals(
            $expectation->getShipperContactCountryISOCode(),
            $order->getShipper()->getContact()->getCountryISOCode()
        );
        $this->assertEquals(
            $expectation->getShipperContactState(),
            $order->getShipper()->getContact()->getState()
        );
        $this->assertEquals(
            $expectation->getShipperContactPhone(),
            $order->getShipper()->getContact()->getPhone()
        );
        $this->assertEquals(
            $expectation->getShipperContactEmail(),
            $order->getShipper()->getContact()->getEmail()
        );
        $this->assertEquals(
            $expectation->getShipperContactContactPerson(),
            $order->getShipper()->getContact()->getContactPerson()
        );

        $this->assertEquals(
            $expectation->getShipperReturnReceiverName1(),
            $order->getShipper()->getReturnReceiver()->getName1()
        );
        $this->assertEquals(
            $expectation->getShipperReturnReceiverName2(),
            $order->getShipper()->getReturnReceiver()->getName2()
        );
        $this->assertEquals(
            $expectation->getShipperReturnReceiverName3(),
            $order->getShipper()->getReturnReceiver()->getName3()
        );
        $this->assertEquals(
            $expectation->getShipperReturnReceiverStreetName(),
            $order->getShipper()->getReturnReceiver()->getStreetName()
        );
        $this->assertEquals(
            $expectation->getShipperReturnReceiverAddressAddition(),
            $order->getShipper()->getReturnReceiver()->getAddressAddition()
        );
        $this->assertEquals(
            $expectation->getShipperReturnReceiverDispatchingInformation(),
            $order->getShipper()->getReturnReceiver()->getDispatchingInformation()
        );
        $this->assertEquals(
            $expectation->getShipperReturnReceiverZip(),
            $order->getShipper()->getReturnReceiver()->getZip()
        );
        $this->assertEquals(
            $expectation->getShipperReturnReceiverCity(),
            $order->getShipper()->getReturnReceiver()->getCity()
        );
        $this->assertEquals(
            $expectation->getShipperReturnReceiverCountry(),
            $order->getShipper()->getReturnReceiver()->getCountry()
        );
        $this->assertEquals(
            $expectation->getShipperReturnReceiverCountryISOCode(),
            $order->getShipper()->getReturnReceiver()->getCountryISOCode()
        );
        $this->assertEquals(
            $expectation->getShipperReturnReceiverState(),
            $order->getShipper()->getReturnReceiver()->getState()
        );
        $this->assertEquals(
            $expectation->getShipperReturnReceiverPhone(),
            $order->getShipper()->getReturnReceiver()->getPhone()
        );
        $this->assertEquals(
            $expectation->getShipperReturnReceiverEmail(),
            $order->getShipper()->getReturnReceiver()->getEmail()
        );
        $this->assertEquals(
            $expectation->getShipperReturnReceiverContactPerson(),
            $order->getShipper()->getReturnReceiver()->getContactPerson()
        );

        $this->assertEquals(
            $expectation->getReceiverName1(),
            $order->getReceiver()->getName1()
        );
        $this->assertEquals(
            $expectation->getReceiverName2(),
            $order->getReceiver()->getName2()
        );
        $this->assertEquals(
            $expectation->getReceiverName3(),
            $order->getReceiver()->getName3()
        );
        $this->assertEquals(
            $expectation->getReceiverStreetName(),
            $order->getReceiver()->getStreetName()
        );
        $this->assertEquals(
            $expectation->getReceiverStreetNumber(),
            $order->getReceiver()->getStreetNumber()
        );
        $this->assertEquals(
            $expectation->getReceiverAddressAddition(),
            $order->getReceiver()->getAddressAddition()
        );
        $this->assertEquals(
            $expectation->getReceiverDispatchingInformation(),
            $order->getReceiver()->getDispatchingInformation()
        );
        $this->assertEquals(
            $expectation->getReceiverZip(),
            $order->getReceiver()->getZip()
        );
        $this->assertEquals(
            $expectation->getReceiverCity(),
            $order->getReceiver()->getCity()
        );
        $this->assertEquals(
            $expectation->getReceiverCountry(),
            $order->getReceiver()->getCountry()
        );
        $this->assertEquals(
            $expectation->getReceiverCountryISOCode(),
            $order->getReceiver()->getCountryISOCode()
        );
        $this->assertEquals(
            $expectation->getReceiverState(),
            $order->getReceiver()->getState()
        );
        $this->assertEquals(
            $expectation->getReceiverPhone(),
            $order->getReceiver()->getPhone()
        );
        $this->assertEquals(
            $expectation->getReceiverEmail(),
            $order->getReceiver()->getEmail()
        );
        $this->assertEquals(
            $expectation->getReceiverContactPerson(),
            $order->getReceiver()->getContactPerson()
        );
        $this->assertEquals(
            $expectation->getPackstationZip(),
            $order->getReceiver()->getPackstation()->getZip()
        );
        $this->assertEquals(
            $expectation->getPackstationCity(),
            $order->getReceiver()->getPackstation()->getCity()
        );
        $this->assertEquals(
            $expectation->getPackstationPackstationNumber(),
            $order->getReceiver()->getPackstation()->getPackstationNumber()
        );
        $this->assertEquals(
            $expectation->getPackstationPostNumber(),
            $order->getReceiver()->getPackstation()->getPostNumber()
        );
        $this->assertNull($order->getReceiver()->getPostfiliale());
        $this->assertNull($order->getReceiver()->getParcelShop());

        $this->assertEquals(
            $expectation->getGlobalSettingsLabelType(),
            $order->getLabelResponseType()
        );

        $this->assertEquals(
            $expectation->getShipmentSettingsDate(),
            $order->getShipmentDate()
        );
        $this->assertEquals(
            $expectation->getShipmentSettingsProduct(),
            $order->getProductCode()
        );

        $this->assertEquals(
            $expectation->getServiceSettingsDayOfDelivery(),
            $order->getServiceSelection()->getDayOfDelivery()
        );
        $this->assertEquals(
            $expectation->getServiceSettingsDeliveryTimeFrame(),
            $order->getServiceSelection()->getDeliveryTimeFrame()
        );
        $this->assertEquals(
            $expectation->getServiceSettingsPreferredLocation(),
            $order->getServiceSelection()->getPreferredLocation()
        );
        $this->assertEquals(
            $expectation->getServiceSettingsPreferredNeighbour(),
            $order->getServiceSelection()->getPreferredNeighbour()
        );
        $this->assertEquals(
            $expectation->getServiceSettingsParcelAnnouncement(),
            $order->getServiceSelection()->getParcelAnnouncement()
        );
        $this->assertEquals(
            $expectation->getServiceSettingsVisualCheckOfAge(),
            $order->getServiceSelection()->getVisualCheckOfAge()
        );
        $this->assertEquals(
            $expectation->isServiceSettingsReturnShipment(),
            $order->getServiceSelection()->isReturnShipment()
        );
        $this->assertEquals(
            $expectation->getServiceSettingsInsurance(),
            $order->getServiceSelection()->getInsurance()
        );
        $this->assertEquals(
            $expectation->isServiceSettingsBulkyGoods(),
            $order->getServiceSelection()->isBulkyGoods()
        );
        $this->assertEquals(
            $expectation->getServiceSettingsCod(),
            $order->getServiceSelection()->getCod()
        );
        $this->assertEquals(
            $expectation->isServiceSettingsPrintOnlyIfCodeable(),
            $order->getServiceSelection()->isPrintOnlyIfCodeable()
        );
    }
}
