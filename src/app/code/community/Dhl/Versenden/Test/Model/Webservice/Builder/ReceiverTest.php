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
 * Dhl_Versenden_Test_Model_Webservice_Builder_ReceiverTest
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Test_Model_Webservice_Builder_ReceiverTest
    extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     */
    public function getReceiver()
    {
        $builder = new Dhl_Versenden_Model_Webservice_Builder_Receiver(array(
            'country_directory' => Mage::getModel('directory/country'),
            'helper' => Mage::helper('dhl_versenden/data')
        ));

        $firstName = 'Foo';
        $lastName = 'Bar';
        $name = "$firstName $lastName";
        $company = 'Foo Inc.';
        $streetName = 'Xx';
        $streetNumber = '111';
        $streetFull = "$streetName $streetNumber";
        $postCode = '12345';
        $city = 'Foo';
        $country = 'DE';
        $telephone = '54321';
        $email = 'a@b.c';

        $address = new Mage_Sales_Model_Quote_Address();
        $address->setFirstname($firstName);
        $address->setLastname($lastName);
        $address->setCompany($company);
        $address->setStreetFull($streetFull);
        $address->setPostcode($postCode);
        $address->setCity($city);
        $address->setCountryId($country);
        $address->setTelephone($telephone);
        $address->setEmail($email);

        $receiver = $builder->getReceiver($address);
        $this->assertSame($name, $receiver->getName1());
        $this->assertSame($company, $receiver->getName2());
        $this->assertSame($streetName, $receiver->getStreetName());
        $this->assertSame($streetNumber, $receiver->getStreetNumber());
        $this->assertSame($postCode, $receiver->getZip());
        $this->assertSame($city, $receiver->getCity());
        $this->assertSame($country, $receiver->getCountryISOCode());
        $this->assertSame($telephone, $receiver->getPhone());
        $this->assertSame($email, $receiver->getEmail());
        $this->assertNull($receiver->getPackstation());
        $this->assertNull($receiver->getPostfiliale());
        $this->assertNull($receiver->getParcelShop());
    }

    /**
     * @test
     */
    public function packStationAddressToReceiver()
    {
        $builder = new Dhl_Versenden_Model_Webservice_Builder_Receiver(array(
            'country_directory' => Mage::getModel('directory/country'),
            'helper' => Mage::helper('dhl_versenden/data')
        ));

        $firstName = 'Foo';
        $lastName = 'Bar';
        $name = "$firstName $lastName";
        $streetName = 'Packstation';
        $streetNumber = '111';
        $streetFull = "$streetName $streetNumber";
        $postCode = '12345';
        $city = 'Foo';
        $country = 'DE';
        $telephone = '54321';
        $email = 'a@b.c';
        $postNumber = '123456';

        $address = new Mage_Sales_Model_Quote_Address();
        $address->setFirstname($firstName);
        $address->setLastname($lastName);
        $address->setCompany($postNumber);
        $address->setStreetFull($streetFull);
        $address->setPostcode($postCode);
        $address->setCity($city);
        $address->setCountryId($country);
        $address->setTelephone($telephone);
        $address->setEmail($email);

        $receiver = $builder->getReceiver($address);
        $this->assertSame($name, $receiver->getName1());
        $this->assertNotNull($receiver->getPackstation());
        $this->assertSame($streetNumber, $receiver->getPackstation()->getPackstationNumber());
        $this->assertSame($postNumber, $receiver->getPackstation()->getPostNumber());
        $this->assertSame($postCode, $receiver->getPackstation()->getZip());
        $this->assertSame($city, $receiver->getPackstation()->getCity());
        $this->assertNull($receiver->getPostfiliale());
        $this->assertNull($receiver->getParcelShop());
    }
    /**
     * @test
     */
    public function postOfficeAddressToReceiver()
    {
        $builder = new Dhl_Versenden_Model_Webservice_Builder_Receiver(array(
            'country_directory' => Mage::getModel('directory/country'),
            'helper' => Mage::helper('dhl_versenden/data')
        ));

        $firstName = 'Foo';
        $lastName = 'Bar';
        $name = "$firstName $lastName";
        $streetName = 'Postfiliale';
        $streetNumber = '888';
        $streetFull = "$streetName $streetNumber";
        $postCode = '12345';
        $city = 'Foo';
        $country = 'DE';
        $telephone = '54321';
        $email = 'a@b.c';
        $postNumber = '654321';

        $address = new Mage_Sales_Model_Quote_Address();
        $address->setFirstname($firstName);
        $address->setLastname($lastName);
        $address->setCompany($postNumber);
        $address->setStreetFull($streetFull);
        $address->setPostcode($postCode);
        $address->setCity($city);
        $address->setCountryId($country);
        $address->setTelephone($telephone);
        $address->setEmail($email);

        $receiver = $builder->getReceiver($address);
        $this->assertSame($name, $receiver->getName1());
        $this->assertNotNull($receiver->getPostfiliale());
        $this->assertSame($streetNumber, $receiver->getPostfiliale()->getPostfilialNumber());
        $this->assertSame($postNumber, $receiver->getPostfiliale()->getPostNumber());
        $this->assertSame($postCode, $receiver->getPostfiliale()->getZip());
        $this->assertSame($city, $receiver->getPostfiliale()->getCity());
        $this->assertNull($receiver->getPackstation());
        $this->assertNull($receiver->getParcelShop());
    }

    /**
     * @test
     */
    public function parcelShopAddressToReceiver()
    {
        $builder = new Dhl_Versenden_Model_Webservice_Builder_Receiver(array(
            'country_directory' => Mage::getModel('directory/country'),
            'helper' => Mage::helper('dhl_versenden/data')
        ));

        $firstName = 'Foo';
        $lastName = 'Bar';
        $name = "$firstName $lastName";
        $streetName = 'Paketshop';
        $streetNumber = '999';
        $streetFull = "$streetName $streetNumber";
        $postCode = '12345';
        $city = 'Foo';
        $country = 'DE';
        $telephone = '54321';
        $email = 'a@b.c';
        $postNumber = '654321';

        $address = new Mage_Sales_Model_Quote_Address();
        $address->setFirstname($firstName);
        $address->setLastname($lastName);
        $address->setCompany($postNumber);
        $address->setStreetFull($streetFull);
        $address->setPostcode($postCode);
        $address->setCity($city);
        $address->setCountryId($country);
        $address->setTelephone($telephone);
        $address->setEmail($email);

        // parcel shops are not handled by this extension
        $receiver = $builder->getReceiver($address);
        $this->assertSame($name, $receiver->getName1());
        $this->assertNull($receiver->getParcelShop());
        $this->assertNull($receiver->getPackstation());
        $this->assertNull($receiver->getPostfiliale());
    }
}
