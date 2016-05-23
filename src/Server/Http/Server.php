<?php
/**
 * Server
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/seven-server
 * @license https://opensource.org/licenses/MIT
 */

namespace Seven\Server\Http;

use Seven\Server\Exception;
use Seven\Server\SocketServer;

class Server extends SocketServer {

    protected $onRequest;

    public function __construct()
    {
        parent::__construct();

        $this->onMessage = [$this, 'onMessage'];
    }

    public function onMessage($client, $message)
    {
        $request = Request::new($message);
        $response = Response::new();

        call_user_func_array($this->onRequest, [$request, $response]);

        socket_write($client, $response->exportRawHttpMessage());
    }

    public function start()
    {
        if (null === $this->onRequest) {
            throw new Exception('Did not set the onRequest event');
        }

        parent::start();
    }

    public function setOnRequest(\Closure $callback)
    {
        $this->onRequest = $callback;
    }

}