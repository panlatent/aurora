<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora\Http;

use Aurora\Http\Client\Events;

class Client extends \Aurora\Client
{
    protected function createConfig()
    {
        return new ClientConfig();
    }

    protected function createEventAcceptor()
    {
        return new Events($this);
    }
}