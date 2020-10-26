<?php

/**
 * See LICENSE.md for license details.
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
