<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

use Aurora\Http\Request;
use Aurora\Http\Response;
use Aurora\Http\Server;
use Aurora\Support\Utils;

/** @var \Aurora\Config $config */
$server = new Server();
$listens = Utils::listens($config->get('bind.listen', '127.0.0.1:10042'));
foreach ($listens as $listen) {
    $server->bind($listen['address'], $listen['port']);
}
$server->listen();
$server->pipe(function(Request $request) {
    yield $request->uri();
//    $app = new Application();
//    yield $app->handle($request);
});

$show = new \Aurora\Pipeline();
$show->pipe(function () {
    $response = Response::factory();
    $response->setRawBody('<html><body>' . yield . '</body></html>');

    return $response;
})->pipe(function (Response $response, \Aurora\Pipeline $pipe) {
    $pipe->client->send($response->getContent());
});

$server->pipeline()->join($show);
$server->start();