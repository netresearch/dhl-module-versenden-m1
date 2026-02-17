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
        $options = [];
        switch ($modelName) {
            case 'PreferredDayAvailable':
                /** @var \Dhl\Versenden\Cig\Model\PreferredDayAvailable $serviceModel */
                $validDays = $serviceModel->getValidDays();
                $timeFormat = 'Y-m-d';
                foreach ($validDays as $day) {
                    /** @var DateTime $endDate */
                    $endDate = $day->getEnd();
                    $options[$endDate->format($timeFormat)] =
                        [
                            'value' => $endDate->format('d-') .
                                Mage::helper('dhl_versenden')->__($endDate->format('D')),
                            'disabled' => false,
                        ];
                }
                break;
        }

        return $options;
    }
}
