<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora\Console\Command;

use Aurora\Config\FileConfig;
use Aurora\Console\Daemon;
use Aurora\Console\DaemonConfig;
use Aurora\Console\Exception;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Command extends \Symfony\Component\Console\Command\Command
{
    /**
     * @var \Aurora\Console\Daemon
     */
    protected $daemon;

    protected function configure()
    {
        $this->addOption('config', 'c', InputArgument::OPTIONAL, 'Look for aurora.ini file in this directory',
            AURORA_ROOT);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->addConsoleOutputColors($output);

        /** @var \Aurora\Console\Application $app */
        $app = $this->getApplication();

        if ( ! ($config = $app->getConfig())) {
            $configPath = $this->getAuroraConfigPath($input->getOption('config'));
            if ( ! is_file($configPath)) {
                throw new Exception("Could not find the configuration file: $configPath");
            }

            FileConfig::create($configPath);
            $config = new DaemonConfig();
        }

        $this->daemon = new Daemon($config);
    }

    protected function addConsoleOutputColors(OutputInterface $output)
    {
        $warning = new OutputFormatterStyle('red', null, []);
        $output->getFormatter()->setStyle('warning', $warning);
    }

    protected function getAuroraConfigPath($path)
    {
        if ('/' != $path[0]) {
            $path = AURORA_PWD . $path;
        }

        if (is_dir($path)) {
            return rtrim($path, '/') . DIRECTORY_SEPARATOR . AURORA_INI;
        } else {
            return $path;
        }
    }

}