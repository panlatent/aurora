<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora\Console;

use Generator;

class Application extends \Symfony\Component\Console\Application
{
    /**
     * @var \Aurora\Config|null
     */
    protected $config;

    /**
     * @var callable
     */
    protected $master;

    public function getConfig()
    {
        return $this->config;
    }

    public function do(Generator $generator)
    {
        $generator->send($this->master);
    }

    public function setMaster($callback)
    {
        $this->master = $callback;
    }

    public function setConfig(Config $config)
    {
        $this->config = $config;
    }

}