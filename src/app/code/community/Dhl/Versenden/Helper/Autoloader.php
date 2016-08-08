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

/**
 * Dhl_Versenden_Helper_Autoloader
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Helper_Autoloader
{
    protected $_prefixes = array();

    /**
     * Adds a base directory for a namespace prefix.
     * For the current project, one base dir per namespace seems sufficient.
     *
     * @param string $prefix The namespace prefix.
     * @param string $baseDir A base directory for class files in the
     * namespace.
     * @return void
     */
    public function addNamespace($prefix, $baseDir)
    {
        // normalize namespace prefix
        $prefix = trim($prefix, '\\') . '\\';

        // normalize the base directory with a trailing separator
        $baseDir = rtrim($baseDir, DIRECTORY_SEPARATOR) . '/';

        // define where the classes for a namespace should be looked up
        $this->_prefixes[$prefix] = $baseDir;
    }

    /**
     * Register loader with SPL autoloader stack.
     *
     * @return void
     */
    public function register()
    {
        spl_autoload_register(array($this, 'loadClass'), true, true);
    }

    /**
     * Load the class file for a given class name.
     *
     * @param string $class The fully-qualified class name.
     * @return bool
     */
    public function loadClass($class)
    {
        reset($this->_prefixes);
        while (list($prefix, $baseDir) = each($this->_prefixes)) {
            $len = strlen($prefix);
            if (strncmp($prefix, $class, $len) !== 0) {
                // class does not match current namespace prefix, go on.
                continue;
            }

            $class = substr($class, $len);
            $phpFile =  $baseDir . str_replace('\\', '/', $class) . '.php';
            if (file_exists($phpFile)) {
                require_once $phpFile;
                return true;
            }
        }

        // class did not match registered namespace prefixes or was not found.
        return false;
    }
}
