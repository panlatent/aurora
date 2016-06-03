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

class ServerStop extends Command {

    protected function configure()
    {
        $this
            ->setName('server:stop')
            ->setDescription('stop server');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $daemon = Daemon::instance();
            $daemon->stop();
            $output->writeln('<info>Server is stop!</info>');
        } catch (Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
        }
    }

}