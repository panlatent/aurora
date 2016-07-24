<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora\Http;

class Server extends \Aurora\Server
{
    public function __construct()
    {
        parent::__construct();

        $this->pipeline->pipe([$this, 'request']);
    }

    public function request($content, $pipe)
    {
        return Request::factory($content);
    }
}