<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Test_Model_AutoloaderTest extends EcomDev_PHPUnit_Test_Case
{
    private const LOG_FILE = 'dhl_versenden_autoloader_test.log';

    protected function tearDown(): void
    {
        $logFile = Mage::getBaseDir('log') . DS . self::LOG_FILE;
        if (file_exists($logFile)) {
            unlink($logFile);
        }

        parent::tearDown();
    }

    /**
     * The module autoloader must not shadow the psr/log package that
     * OpenMage's Monolog logger implements. A clash is a fatal error,
     * so the scenario runs in a separate PHP process.
     *
     * @test
     */
    public function registeredAutoloaderKeepsShopLoggingIntact()
    {
        $script = sprintf(
            <<<'PHP'
                <?php
                require %s;
                Mage::app('admin');
                Dhl_Versenden_Model_Autoloader::register();
                Mage::log('autoloader test', Zend_Log::DEBUG, %s, true);
                if (class_exists(\Monolog\Logger::class)) {
                    new \Monolog\Logger('dhl_versenden');
                }
                echo 'LOGGING_OK';
                PHP,
            var_export(Mage::getRoot() . DS . 'Mage.php', true),
            var_export(self::LOG_FILE, true),
        );

        $process = proc_open(
            [PHP_BINARY],
            [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes,
        );
        fwrite($pipes[0], $script);
        fclose($pipes[0]);
        $output = stream_get_contents($pipes[1]) . stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $exitCode = proc_close($process);

        static::assertSame(0, $exitCode, $output);
        static::assertStringContainsString('LOGGING_OK', $output);
    }
}
