<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Helper_Autoloader
{
    protected $_prefixes = [];

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
        spl_autoload_register([$this, 'loadClass'], true, true);
    }

    /**
     * Load the class file for a given class name.
     *
     * @param string $class The fully-qualified class name.
     * @return bool
     */
    public function loadClass($class)
    {
        foreach ($this->_prefixes as $prefix => $baseDir) {
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
