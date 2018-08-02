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
 * @author    andreas <andreas.mueller@netresearch.de>
 * @copyright 2018 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://www.netresearch.de/
 */

/**
 * Dhl_Versenden_Model_Services_PreferredDay
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Andreas Müller<andreas.mueller@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     https://www.netresearch.de/
 */
class Dhl_Versenden_Model_Services_ServiceOptions
{
    /**
     * @param \Dhl\Versenden\Cig\Model\ModelInterface $serviceModel
     * @return false | array
     */
    public function getOptions($serviceModel)
    {
        $modelName = $serviceModel->getModelName();
        $options = array();
        switch ($modelName) {
            case 'PreferredDayAvailable':
                /** @var \Dhl\Versenden\Cig\Model\PreferredDayAvailable $serviceModel */
                $validDays = $serviceModel->getValidDays();
                $timeFormat = 'Y-m-d';
                foreach ($validDays as $day) {
                    /** @var DateTime $endDate */
                    $endDate = $day->getEnd();
                    $options[$endDate->format($timeFormat)] =
                        array(
                            'value' => $endDate->format('d-').
                                Mage::helper('dhl_versenden')->__($endDate->format('D')),
                            'disabled' => false
                        );
                }
                break;
            case 'PreferredTimeAvailable':
                /** @var \Dhl\Versenden\Cig\Model\PreferredTimeAvailable $serviceModel */
                $timeFrames = $serviceModel->getTimeframes();
                foreach ($timeFrames as $timeFrame) {
                    $timeFrameStart = explode(':', $timeFrame->getStart());
                    $timeFrameEnd = explode(':', $timeFrame->getEnd());
                    $start = array_shift($timeFrameStart);
                    $end = array_shift($timeFrameEnd);
                    $key = $start.'00'.$end.'00';
                    $options[$key] = Mage::helper('dhl_versenden')->__($start .' - '.$end);
                }
        }

        return $options;
    }
}
