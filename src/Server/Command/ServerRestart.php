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

class ServerRestart extends Command {

    protected function configure()
    {
        $this
            ->setName('server:restart')
            ->setDescription('restart server');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $daemon = Daemon::instance();
            if ($pid = $daemon->status()) {
                $daemon->stop();
                $output->writeln('<info>Server is stopped!</info>');
            }

            $daemon->start();
            $output->writeln('<info>Server is running!</info>');
        } catch (Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
        }
    }

}