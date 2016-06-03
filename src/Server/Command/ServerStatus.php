<?php
/**
 * Server
 */

namespace Panlatent\Server\Command;

use Panlatent\Server\Daemon;
use Panlatent\Server\Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ServerStatus extends Command {

    protected function configure()
    {
        $this
            ->setName('server:status')
            ->setDescription('server status');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $daemon = Daemon::instance();
            if ($daemon->status()) {
                $output->writeln('<info>Server is running</info>');
            } else {
                $output->writeln('<error>Server is stopped</error>');
            }
        } catch (Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
        }
    }

}