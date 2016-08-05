<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora\Http;

/**
 * Class Server
 * @package Aurora\Http
 */
class Server extends \Aurora\Server
{
    public static function createMatchPipeline()
    {
        return new Pipeline();
    }

    public function createWorker($client)
    {
        return new Worker($this, $this->event, $client);
    }
}