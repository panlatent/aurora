<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ServerStatus extends Command
{

    protected function configure()
    {
        parent::configure();
        $this
            ->setName('server:status')
            ->setAliases(['status'])
            ->setDescription('server status');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            parent::execute($input, $output);
            if (($status = $this->daemon->status())) {
                $output->writeln("<info>Aurora is working! Master Process PID:[{$status['pid']}]</info>");
            } else {
                $output->writeln('<error>Aurora has stopped working!</error>');
            }
        } catch (\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
        }
    }

}