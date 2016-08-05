<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora\Http;

use Aurora\Client\WriteBuffer;
use Aurora\Config;
use Aurora\Event\Dispatcher;
use Aurora\Http\Client\Events as ClientEvents;

class Client extends \Aurora\Client
{
    public function __construct(Worker $worker, Dispatcher $event, $socket, Pipeline $pipeline,
                                Config $config = null, WriteBuffer $writeBuffer = null)
    {
        parent::__construct($worker, $event, $socket, $pipeline, $config, $writeBuffer);

        $this->writeBuffer->open();
    }

    protected function createConfig()
    {
        return new ClientConfig();
    }

    protected function createEventAcceptor()
    {
        return new ClientEvents($this->event, $this);
    }
}