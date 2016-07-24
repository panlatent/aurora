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

if ( ! defined('AURORA_DAEMON')) {
    define('AURORA_DAEMON', false);
    require __DIR__ . '/vendor/autoload.php';
}

$server = new Server();
$server->bind('127.0.0.1', 10042);
$server->listen();
$server->pipe(function(Request $request) {
    yield 'HTTP_HOST: ' . $request->header('HTTP_HOST');
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