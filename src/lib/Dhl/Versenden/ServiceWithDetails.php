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
namespace Dhl\Versenden;
use Dhl\Versenden\Service as AbstractService;

/**
 * ServiceWithDetails
 *
 * @category Dhl
 * @package  Dhl\Versenden\Service
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
abstract class ServiceWithDetails extends AbstractService
{
    /** @var string */
    public $frontendInputType = self::INPUT_TYPE_TEXT;
    /** @var string */
    public $placeholder = '';

    public function __construct($value = '', $placeholder = '')
    {
        $this->placeholder = $placeholder;

        parent::__construct($value);
    }

    /**
     * Get Placeholder from Service
     *
     * @return string
     */
    public function getPlaceholder()
    {
        return $this->placeholder;
    }

    /**
     * @return int
     */
    public function getMaxLength()
    {
        return 100;
    }
}
