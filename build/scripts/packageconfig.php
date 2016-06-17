<?php
/**
 * Netresearch Build
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
 * @category  Netresearch
 * @package   Netresearch_Build
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */
namespace Netresearch\Build;
/**
 * PackageConfig
 *
 * Adjust at least the following settings:
 * - VENDOR_NAME
 * - MODULE_NAME
 * - summary
 * - description
 * - notes
 *
 * @category Netresearch
 * @package  Netresearch_Build
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class PackageConfig
{
    const VENDOR_NAME = 'Dhl';
    const MODULE_NAME = 'Versenden';

    protected $baseDir;
    protected $archiveFiles;
    protected $extensionName;
    protected $extensionVersion;
    protected $pathOutput;
    protected $stability = 'stable';
    protected $license = 'OSL3';
    protected $channel = 'community';
    protected $summary = 'DHL Versenden';
    protected $description = 'This extension integrates the DHL business customer shipping API into the order processing workflow.';
    protected $notes = 'Official DHL Versenden extension';
    protected $authorName = 'Christoph Aßmann';
    protected $authorUser = 'christoph_';
    protected $authorEmail = 'christoph.assmann@netresearch.de';
    protected $phpMin = '5.4.0';
    protected $phpMax = '7.9.0';
    protected $skipVersionCompare = false;

    /**
     * PackageConfig constructor. Add dynamic config params.
     *
     * @param string $vendorName
     * @param string $moduleName
     */
    public function __construct($vendorName, $moduleName)
    {
        $workspaceRoot = $this->getWorkspaceRoot();
        $version       = $this->getExtensionVersion($workspaceRoot, $vendorName, $moduleName);

        $this->baseDir          = $workspaceRoot;
        $this->pathOutput       = "$workspaceRoot/var/connect/";
        $this->extensionVersion = $version;
        $this->extensionName    = $this->getExtensionName($vendorName, $moduleName);
        $this->archiveFiles     = $this->getArchiveFiles($workspaceRoot, $moduleName, $version);
    }

    /**
     * Obtain the extension's base directory.
     *
     * @return string
     */
    protected function getWorkspaceRoot()
    {
        return realpath(__DIR__ . '/../../');
    }

    /**
     * Calculate the current extension's name
     *
     * @param string $vendorName
     * @param string $moduleName
     * @return string
     */
    protected function getExtensionName($vendorName, $moduleName)
    {
        return sprintf('%s_%s', $vendorName, $moduleName);
    }

    /**
     * Read the extension's version from the main config.xml file
     *
     * @param string $workspaceRoot
     * @param string $vendorName
     * @param string $moduleName
     * @return string
     */
    protected function getExtensionVersion($workspaceRoot, $vendorName, $moduleName)
    {
        $configFile = sprintf(
            "%s/src/app/code/community/%s/%s/etc/config.xml",
            $workspaceRoot,
            $vendorName,
            $moduleName
        );

        $xml     = simplexml_load_file($configFile);
        $version = $xml->xpath('/config/modules/*/version');
        return (string)$version[0];
    }

    /**
     * Obtain the tar archive name from the previous "make tar" step (see Makefile)
     *
     * @param string $workspaceRoot
     * @param string $moduleName
     * @param string $version
     * @return string
     * @throws \Exception
     */
    protected function getArchiveFiles($workspaceRoot, $moduleName, $version)
    {
        $archive = sprintf('%s/%s-%s.tar', $workspaceRoot, $moduleName, $version);
        if (!file_exists($archive)) {
            throw new \Exception("File not found: $archive");
        }
        return basename($archive);
    }

    /**
     * Obtain the extension configuration as input for Alan Storm's tar to connect script
     * @link http://alanstorm.com/magento_connect_from_tar
     *
     * @return mixed[]
     */
    public function getConfig()
    {
        return [
            'base_dir'             => $this->baseDir,
            'archive_files'        => $this->archiveFiles,
            'extension_name'       => $this->extensionName,
            'extension_version'    => $this->extensionVersion,
            'path_output'          => $this->pathOutput,
            'stability'            => $this->stability,
            'license'              => $this->license,
            'channel'              => $this->channel,
            'summary'              => $this->summary,
            'description'          => $this->description,
            'notes'                => $this->notes,
            'author_name'          => $this->authorName,
            'author_user'          => $this->authorUser,
            'author_email'         => $this->authorEmail,
            'php_min'              => $this->phpMin,
            'php_max'              => $this->phpMax,
            'skip_version_compare' => false,
        ];
    }
}

$pc = new PackageConfig(PackageConfig::VENDOR_NAME, PackageConfig::MODULE_NAME);
$configData = $pc->getConfig();

return $configData;
