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
 * @package   Dhl\Versenden\Service
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */
namespace Dhl\Versenden\Service;
use Dhl\Versenden\ServiceWithOptions as OptionsService;

/**
 * DeliveryTimeFrame
 *
 * @category Dhl
 * @package  Dhl\Versenden\Service
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class DeliveryTimeFrame extends OptionsService
{
    /**
     * DeliveryTimeFrame constructor.
     * @param string $value
     * @param array $options
     */
    public function __construct($value = '', $options = [])
    {
        parent::__construct($value, $options);

        $this->name = 'Delivery Time Frame';
        $this->isCustomerService = true;
    }

    /**
     * @return string[]
     */
    public function getOptions()
    {
        //TODO(nr): How to realize translations in lib?
        return [
            '10001200' => '10:00 - 12:00',
            '12001400' => '12:00 - 14:00',
            '14001600' => '14:00 - 16:00',
            '16001800' => '16:00 - 18:00',
            '18002000' => '18:00 - 20:00',
            '19002100' => '19:00 - 21:00',
        ];
    }
}
