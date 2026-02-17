<?php

/**
 * See LICENSE.md for license details.
 */

use Dhl\Versenden\ParcelDe\Config\Data\Shipper;
use Dhl\Versenden\ParcelDe\Product;

class Dhl_Versenden_Model_Webservice_Builder_Shipper
{
    /** @var Dhl_Versenden_Model_Config_Shipper */
    protected $config;

    /** @var Mage_Sales_Model_Order_Shipment */
    protected $shipment;

    /**
     * Dhl_Versenden_Model_Webservice_Builder_Shipper constructor.
     * @param Dhl_Versenden_Model_Config[] $args
     * @throws Mage_Core_Exception
     */
    public function __construct($args)
    {
        $argName = 'config';
        if (!isset($args[$argName])) {
            Mage::throwException("required argument missing: $argName");
        }
        if (!$args[$argName] instanceof Dhl_Versenden_Model_Config_Shipper) {
            Mage::throwException("invalid argument: $argName");
        }
        $this->config = $args[$argName];
    }

    /**
     * Build shipper data using the SDK request builder
     *
     * @param \Dhl\Sdk\ParcelDe\Shipping\Api\ShipmentOrderRequestBuilderInterface $sdkBuilder
     * @param mixed $store
     * @param string $productCode DHL product code (e.g., 'V01PAK', 'V62KP', 'V53WPAK')
     * @param bool $includeReturnShipment Whether the ReturnShipment service is selected
     * @return void
     */
    public function build(\Dhl\Sdk\ParcelDe\Shipping\Api\ShipmentOrderRequestBuilderInterface $sdkBuilder, $store, $productCode, bool $includeReturnShipment = false)
    {
        // Get account settings
        $account = $this->config->getAccountSettings();

        // Billing number format: EKP (10 chars) + Procedure (2 chars) + Participation (2 chars) = 14 chars
        // Example: 3333333333 + 01 + 02 = 33333333330102
        // Procedure codes are determined dynamically from the product code
        $procedure = Product::getProcedure($productCode);
        $returnProcedure = Product::getProcedureReturn($productCode);

        $billingNumber = $account->getEkp() . $procedure . $account->getParticipation($procedure);
        $returnBillingNumber = ($includeReturnShipment && $returnProcedure)
            ? $account->getEkp() . $returnProcedure . $account->getParticipation($returnProcedure)
            : null;

        // Set shipper account
        $sdkBuilder->setShipperAccount($billingNumber, $returnBillingNumber);

        // Get contact information
        $contact = $this->config->getContact($store);

        // Convert country code from ISO-2 (DE) to ISO-3 (DEU) for REST API
        // The BCS/SOAP Contact model uses ISO-2, but REST SDK Address requires ISO-3
        $countryIso2 = $contact->getCountryISOCode();
        $countryDirectory = Mage::getSingleton('directory/country')->loadByCode($countryIso2);
        $countryIso3 = $countryDirectory->getIso3Code();

        // Set shipper address
        $sdkBuilder->setShipperAddress(
            $contact->getName1(),                    // company
            $countryIso3,                           // countryCode (ISO-3)
            $contact->getZip(),                     // postalCode
            $contact->getCity(),                    // city
            $contact->getStreetName(),              // streetName
            $contact->getStreetNumber(),            // streetNumber
            $contact->getName2(),                   // name
            $contact->getName3(),                   // nameAddition
            $contact->getEmail(),                   // email
            $contact->getPhone(),                   // phone
            $contact->getContactPerson(),           // contactPerson
            $contact->getState(),                   // state
            $contact->getDispatchingInformation(),  // dispatchingInformation
            [$contact->getAddressAddition()],        // addressAddition
        );

        // Get and set bank data if available
        $bankRefMap = [];
        if ($this->shipment !== null) {
            $bankRefMap = $this->getBankRefMap();
        }

        $bankData = $this->config->getBankData($store, $bankRefMap);
        if ($bankData) {
            // Use account reference instead of explicit bank details to avoid
            // "elevated privileges" requirement. Per DHL REST API schema:
            // "Providing account information explicitly requires elevated privileges"
            // Account references are maintained in DHL Business Customer Portal
            $sdkBuilder->setShipperBankData(
                null,  // accountOwner - use account reference instead
                null,  // bankName - use account reference instead
                null,  // iban - use account reference instead
                null,  // bic - use account reference instead
                $bankData->getAccountReference(),
                [$bankData->getNote1(), $bankData->getNote2()],
            );
        }

        // Set return address only for products that support returns (have a return procedure)
        // International products (V53WPAK, V54EPAK, V66WPI, etc.) don't have return procedures
        // Setting a return address without a billing number causes SDK errors
        if ($returnBillingNumber !== null) {
            $returnReceiver = $this->config->getReturnReceiver($store);

            // Convert country code from ISO-2 to ISO-3 for REST API
            $returnCountryIso2 = $returnReceiver->getCountryISOCode();
            $returnCountryDirectory = Mage::getSingleton('directory/country')->loadByCode($returnCountryIso2);
            $returnCountryIso3 = $returnCountryDirectory->getIso3Code();

            $sdkBuilder->setReturnAddress(
                $returnReceiver->getName1(),                    // company
                $returnCountryIso3,                             // countryCode (ISO-3)
                $returnReceiver->getZip(),                     // postalCode
                $returnReceiver->getCity(),                    // city
                $returnReceiver->getStreetName(),              // streetName
                $returnReceiver->getStreetNumber(),            // streetNumber
                $returnReceiver->getName2(),                   // name
                $returnReceiver->getName3(),                   // nameAddition
                $returnReceiver->getEmail(),                   // email
                $returnReceiver->getPhone(),                   // phone
                $returnReceiver->getContactPerson(),           // contactPerson
                $returnReceiver->getState(),                   // state
                $returnReceiver->getDispatchingInformation(),  // dispatchingInformation
                [$returnReceiver->getAddressAddition()],        // addressAddition
            );
        }
    }

    /**
     * @param Mage_Sales_Model_Order_Shipment $shipment
     * @return $this
     */
    public function setShipment(Mage_Sales_Model_Order_Shipment $shipment)
    {
        $this->shipment = $shipment;
        return $this;
    }

    /**
     * @return array
     */
    protected function getBankRefMap()
    {
        // Available placeholders for the bank data configuration
        return [
            '%orderId%'      => $this->shipment->getOrder()->getIncrementId(),
            '%customerId%'   => $this->shipment->getOrder()->getCustomerId(),
        ];
    }
}
