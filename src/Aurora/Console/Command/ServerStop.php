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

class ServerStop extends Command
{

    protected function configure()
    {
        parent::configure();
        $this
            ->setName('server:stop')
            ->setAliases(['stop'])
            ->setDescription('stop server');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            parent::execute($input, $output);
            if (false === ($status = $this->daemon->status())) {
                throw new Exception("Error: Aurora is not working!");
            }
            if ( ! $this->daemon->stop()) {
                throw new Exception("Error: Unable to make Aurora stop working!");
            }
            $output->writeln('<warning>Aurora has stopped working!</warning>');
        } catch (\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
        }
    }

}