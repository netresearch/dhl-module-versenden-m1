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
use Dhl\Versenden\Service as AbstractService;

/**
 * Insurance
 *
 * @category Dhl
 * @package  Dhl\Versenden\Service
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Insurance extends AbstractService
{
    const TYPE_A = 'A';
    const TYPE_B = 'B';

    /** @var string */
    public $frontendInputType = self::INPUT_TYPE_SELECT;

    /**
     * Insurance constructor.
     * @param string $value
     */
    public function __construct($value = '')
    {
        parent::__construct($value);

        $this->name = 'Insurance';
        $this->isCustomerService = false;
    }

    /**
     * @return string[]
     */
    public function getOptions()
    {
        //TODO(nr): How to realize localization in lib?
        return [
            self::TYPE_A => '2.500',
            self::TYPE_B => '25.000',
        ];
    }
}
