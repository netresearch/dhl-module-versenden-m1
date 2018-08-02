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
use \Dhl\Versenden\Bcs\Api\Shipment\Service;
/**
 * Dhl_Versenden_Block_Adminhtml_Sales_Order_Shipment_Service_Edit
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Block_Adminhtml_Sales_Order_Shipment_Service_Edit
    extends Dhl_Versenden_Block_Adminhtml_Sales_Order_Shipment_Service
{
    /**
     * @var Dhl_Versenden_Model_Config
     */
    private $config;

    /**
     * @var Dhl_Versenden_Model_Config_Service
     */
    private $serviceConfig;

    /**
     * @var Dhl_Versenden_Model_Config_Shipment
     */
    private $shipmentConfig;

    /**
     * @var Dhl_Versenden_Helper_Data
     */
    private $helper;

    /**
     * @var Dhl_Versenden_Model_Services_Processor
     */
    private $serviceProcessor;

    /**
     * Dhl_Versenden_Block_Checkout_Onepage_Shipping_Method_Service constructor.
     *
     * @param array $args
     */
    public function __construct(array $args = array())
    {
        $this->config = Mage::getModel('dhl_versenden/config');
        $this->serviceConfig = Mage::getModel('dhl_versenden/config_service');
        $this->shipmentConfig = Mage::getModel('dhl_versenden/config_shipment');
        $this->helper = $this->helper('dhl_versenden/data');
        $this->serviceProcessor = Mage::getModel(
            'dhl_versenden/services_processor',
            array('quote' => $this->getShipment()->getOrder())
        );

        parent::__construct($args);
    }

    /**
     * Obtain the services that are enabled via config. Customer's service
     * selection from checkout is read from shipping address. Services from
     * config are added.
     *
     * @return Service\Collection|Service\Type\Generic[]
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
            $storeId
        );

        $availableServices = $this->serviceProcessor->processServices($availableServices);

        $this->setPrintOnlyIfCodeableService($storeId, $availableServices);

        $this->setParcelAnnouncementService($availableServices);

        /** @var \Dhl\Versenden\Bcs\Api\Info $versendenInfo */
        $versendenInfo = $shippingAddress->getData('dhl_versenden_info');
        if ($versendenInfo instanceof \Dhl\Versenden\Bcs\Api\Info) {
            $this->overrideServiceSelections($availableServices, $versendenInfo);
        }

        return $availableServices;
    }

    /**
     * @param Service\Type\Generic $service
     * @return Service\Type\Renderer
     */
    public function getRenderer(Service\Type\Generic $service)
    {
        return new Service\Type\Renderer($service);
    }

    /**
     * @param string $storeId
     * @param Service\Collection $availableServices
     */
    protected function setPrintOnlyIfCodeableService($storeId, Service\Collection $availableServices)
    {
        $printOnlyIfCodeable = $this->shipmentConfig->getSettings($storeId)
            ->isPrintOnlyIfCodeable();
        $availableServices->getItem(Service\PrintOnlyIfCodeable::CODE)->setValue($printOnlyIfCodeable);
    }

    /**
     * @param $availableServices
     */
    protected function setParcelAnnouncementService(Service\Collection $availableServices)
    {
        $parcelAnnouncement = $availableServices->getItem(Service\ParcelAnnouncement::CODE);
        if (($parcelAnnouncement instanceof Service\ParcelAnnouncement) && !$parcelAnnouncement->isCustomerService()) {
            $availableServices->getItem(Service\ParcelAnnouncement::CODE)->setValue(true);
        }
    }

    /**
     * @param Service\Collection $availableServices
     * @param \Dhl\Versenden\Bcs\Api\Info $versendenInfo
     */
    protected function overrideServiceSelections(
        Service\Collection $availableServices,
        \Dhl\Versenden\Bcs\Api\Info $versendenInfo
    )
    {
        /** @var Service\Type\Generic $availableService */
        foreach ($availableServices as $availableService) {
            $code = $availableService->getCode();
            $serviceSelection = $versendenInfo->getServices()->{$code};
            if ($serviceSelection !== null) {
                $availableService->setValue($serviceSelection);
            }
        }
    }
}
