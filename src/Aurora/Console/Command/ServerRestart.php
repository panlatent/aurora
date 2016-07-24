<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora\Console\Command;

use Aurora\Console\Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ServerRestart extends Command
{

    protected function configure()
    {
        parent::configure();
        $this
            ->setName('server:restart')
            ->setAliases(['restart'])
            ->setDescription('restart server');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            parent::execute($input, $output);

            if (false !== $this->daemon->status()) {
                if ( ! $this->daemon->stop()) {
                    throw new Exception("Error: Unable to make Aurora stop working!");
                }
                $output->writeln('<warning>Aurora has stopped working!</warning>');
            }

            /** @var \Aurora\Console\Application $application */
            $application = $this->getApplication();
            $this->daemon->start($application->getChildProcess());
            $output->writeln('<info>Aurora is working!</info>');
        } catch (\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
        }
    }

}