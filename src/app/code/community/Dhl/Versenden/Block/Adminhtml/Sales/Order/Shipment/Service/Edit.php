<?php

/**
 * See LICENSE.md for license details.
 */

use Dhl\Versenden\ParcelDe\Service;

class Dhl_Versenden_Block_Adminhtml_Sales_Order_Shipment_Service_Edit extends Dhl_Versenden_Block_Adminhtml_Sales_Order_Shipment_Service
{
    /**
     * @var Dhl_Versenden_Model_Config
     */
    protected $config;

    /**
     * @var Dhl_Versenden_Model_Config_Service
     */
    protected $serviceConfig;

    /**
     * @var Dhl_Versenden_Model_Config_Shipment
     */
    protected $shipmentConfig;

    /**
     * @var Dhl_Versenden_Helper_Data
     */
    protected $helper;

    /**
     * @var Dhl_Versenden_Model_Services_Processor
     */
    protected $serviceProcessor;

    /**
     * Dhl_Versenden_Block_Adminhtml_Sales_Order_Shipment_Service_Edit constructor.
     *
     * @param array $args
     */
    public function __construct(array $args = [])
    {
        $this->config = Mage::getModel('dhl_versenden/config');
        $this->serviceConfig = Mage::getModel('dhl_versenden/config_service');
        $this->shipmentConfig = Mage::getModel('dhl_versenden/config_shipment');
        $this->helper = $this->helper('dhl_versenden/data');
        $this->serviceProcessor = Mage::getModel(
            'dhl_versenden/services_processor',
            ['quote' => $this->getShipment()->getOrder()],
        );

        parent::__construct($args);
    }

    /**
     * Obtain the services that are enabled via config. Customer's service
     * selection from checkout is read from shipping address. Services from
     * config are added.
     *
     * @return Service\Collection
     */
    public function getServices()
    {
        // obtain enabled services
        $storeId = $this->getShipment()->getStoreId();
        $shippingAddress = $this->getShipment()->getShippingAddress();

        $shipperCountry = $this->config->getShipperCountry($storeId);
        $recipientCountry = $shippingAddress->getCountryId();
        $isPostalFacility = $this->helper->isPostalFacility($shippingAddress);

        $availableServices = $this->serviceConfig->getAvailableServices(
            $shipperCountry,
            $recipientCountry,
            $isPostalFacility,
            false,
            $storeId,
            true, // packaging popup: show all services regardless of checkout config
        );

        $availableServices = $this->serviceProcessor->processServices($availableServices);

        $this->setParcelAnnouncementService($availableServices);
        $this->setCodServiceFromPaymentMethod($availableServices, $storeId);

        /** @var \Dhl\Versenden\ParcelDe\Info $versendenInfo */
        $versendenInfo = $shippingAddress->getData('dhl_versenden_info');
        if ($versendenInfo instanceof \Dhl\Versenden\ParcelDe\Info) {
            $this->overrideServiceSelections($availableServices, $versendenInfo);
        }

        $this->removeUnselectedCustomerParcelAnnouncement($availableServices);
        $this->applyClosestDropPointToDeliveryType($availableServices, $versendenInfo);

        // Fall back to order's shipping email for POR if no value was set
        $por = $availableServices->getItem(Service\ParcelOutletRouting::CODE);
        if ($por instanceof Service\ParcelOutletRouting && !$por->getValue()) {
            $email = $shippingAddress->getEmail();
            if ($email) {
                $por->setDefaultValue($email);
            }
        }

        return $availableServices;
    }

    /**
     * @param Service\Type\Generic $service
     * @return Service\Type\Renderer
     */
    public function getRenderer(Service\Type\Generic $service)
    {
        if ($service->getCode() === Service\Cod::CODE
            || $service->getCode() === Service\ParcelAnnouncement::CODE
        ) {
            $renderer = new Service\Type\Renderer($service, true);
            $renderer->setSelectedYes('');
            $renderer->setSelectedNo('');
            return $renderer;
        }

        return new Service\Type\Renderer($service, false, true);
    }

    /**
     * Select or remove COD based on the order's payment method.
     *
     * COD is determined solely by the payment method configuration,
     * not by admin choice in the packaging popup (aligning with M2 behavior).
     *
     * @param Service\Collection $availableServices
     * @param mixed $storeId
     */
    protected function setCodServiceFromPaymentMethod(Service\Collection $availableServices, $storeId)
    {
        $codService = $availableServices->getItem(Service\Cod::CODE);
        if (!$codService instanceof Service\Cod) {
            return;
        }

        $order = $this->getShipment()->getOrder();
        $paymentMethod = $order->getPayment() ? $order->getPayment()->getMethod() : null;

        if ($paymentMethod && $this->shipmentConfig->isCodPaymentMethod($paymentMethod, $storeId)) {
            $codService->setValue('1');
            $codService->setDefaultValue('');
        } else {
            $availableServices->removeItem(Service\Cod::CODE);
        }
    }

    /**
     * @param $availableServices
     */
    protected function setParcelAnnouncementService(Service\Collection $availableServices)
    {
        $parcelAnnouncement = $availableServices->getItem(Service\ParcelAnnouncement::CODE);
        if (($parcelAnnouncement instanceof Service\ParcelAnnouncement) && !$parcelAnnouncement->isCustomerService()) {
            $availableServices->getItem(Service\ParcelAnnouncement::CODE)->setValue('1');
        }
    }

    /**
     * @param Service\Collection $availableServices
     * @param \Dhl\Versenden\ParcelDe\Info $versendenInfo
     */
    protected function overrideServiceSelections(
        Service\Collection $availableServices,
        \Dhl\Versenden\ParcelDe\Info $versendenInfo
    ) {
        /** @var Service\Type\Generic $availableService */
        foreach ($availableServices as $availableService) {
            $code = $availableService->getCode();
            $serviceSelection = $versendenInfo->getServices()->{$code};
            if ($serviceSelection !== null) {
                $availableService->setValue($serviceSelection);
            }
        }
    }

    /**
     * Remove parcelAnnouncement from popup when it is a customer service
     * and the customer did not select it during checkout.
     *
     * Matches M2's RemoveUnusedConsumerServicesProcessor behavior.
     *
     * @param Service\Collection $availableServices
     */
    protected function removeUnselectedCustomerParcelAnnouncement(Service\Collection $availableServices)
    {
        $pa = $availableServices->getItem(Service\ParcelAnnouncement::CODE);
        if ($pa instanceof Service\ParcelAnnouncement
            && $pa->isCustomerService()
            && !$pa->isSelected()
        ) {
            $availableServices->removeItem(Service\ParcelAnnouncement::CODE);
        }
    }

    /**
     * Translate customer's ClosestDropPoint checkout selection into DeliveryType=CDP.
     *
     * Matches M2's DeliveryTypeServiceProcessor: when the customer selected CDP
     * during checkout, filter DeliveryType radio to CDP only and pre-select it.
     * ClosestDropPoint itself is always removed from the admin collection (it's
     * a checkout-only concept; admin interacts with DeliveryType directly).
     *
     * @param Service\Collection $availableServices
     * @param mixed $versendenInfo
     */
    protected function applyClosestDropPointToDeliveryType(
        Service\Collection $availableServices,
        $versendenInfo = null
    ) {
        // Always remove ClosestDropPoint from admin — admin uses DeliveryType directly
        $availableServices->removeItem(Service\ClosestDropPoint::CODE);

        if (!$versendenInfo instanceof \Dhl\Versenden\ParcelDe\Info) {
            return;
        }

        if (!$versendenInfo->getServices()->closestDropPoint) {
            return;
        }

        // Customer selected CDP at checkout → lock DeliveryType to CDP
        $deliveryType = $availableServices->getItem(Service\DeliveryType::CODE);
        if ($deliveryType instanceof Service\DeliveryType) {
            $deliveryType->setOptions([
                Service\DeliveryType::CDP => $this->helper->__('Closest Drop Point'),
            ]);
            $deliveryType->setValue(Service\DeliveryType::CDP);
        }
    }
}
