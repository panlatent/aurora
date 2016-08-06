<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora\Http;

class Worker extends \Aurora\Worker
{
    protected function createClient()
    {
        $client = new Client($this, $this->event, $this->socket, $this->server->getPipeline());

        return $client;
    }
}