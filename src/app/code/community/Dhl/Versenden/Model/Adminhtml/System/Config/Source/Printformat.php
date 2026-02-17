<?php

/**
 * See LICENSE.md for license details.
 */

use Dhl\Sdk\ParcelDe\Shipping\Api\Data\OrderConfigurationInterface;

class Dhl_Versenden_Model_Adminhtml_System_Config_Source_Printformat
{
    /**
     * Get available print format options for label printing.
     *
     * Returns all supported label print formats as defined in the DHL REST API.
     * These formats correspond to different label sizes and layouts.
     *
     * @return string[][] Array of ['value' => format_code, 'label' => format_description]
     */
    public function toOptionArray()
    {
        return [
            ['value' => OrderConfigurationInterface::PRINT_FORMAT_A4, 'label' => 'A4 (210x297mm)'],
            ['value' => OrderConfigurationInterface::PRINT_FORMAT_910_300_600, 'label' => '910-300-600'],
            ['value' => OrderConfigurationInterface::PRINT_FORMAT_910_300_610, 'label' => '910-300-610'],
            ['value' => OrderConfigurationInterface::PRINT_FORMAT_910_300_700, 'label' => '910-300-700'],
            ['value' => OrderConfigurationInterface::PRINT_FORMAT_910_300_700_OZ, 'label' => '910-300-700-oz'],
            ['value' => OrderConfigurationInterface::PRINT_FORMAT_910_300_710, 'label' => '910-300-710'],
            ['value' => OrderConfigurationInterface::PRINT_FORMAT_910_300_300, 'label' => '910-300-300'],
            ['value' => OrderConfigurationInterface::PRINT_FORMAT_910_300_300_OZ, 'label' => '910-300-300-oz'],
            ['value' => OrderConfigurationInterface::PRINT_FORMAT_910_300_400, 'label' => '910-300-400'],
            ['value' => OrderConfigurationInterface::PRINT_FORMAT_910_300_410, 'label' => '910-300-410'],
            ['value' => OrderConfigurationInterface::PRINT_FORMAT_100X70, 'label' => '100x70mm'],
        ];
    }
}
