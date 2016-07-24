<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

use Aurora\Http\Request;
use Aurora\Http\Server;

if ( ! defined('AURORA_DAEMON')) {
    define('AURORA_DAEMON', false);
    require __DIR__ . '/vendor/autoload.php';
}

/** @var \Aurora\Pipeline $show */
$show = (function () {
    $pipeline = new \Aurora\Pipeline();
    $pipeline->pipe(function ($body) {
        $response = \Aurora\Http\Response::factory();
        $response->setRawBody('<html><body>' . $body . '</body></html>');

        return $response;
    });
    $pipeline->pipe(function (\Aurora\Http\Response $response, \Aurora\Pipeline $pipe) {
        $pipe->client->send($response->getContent());
    });

    return $pipeline;
})();

$server = new Server();
$server->bind('127.0.0.1', 10042);
$server->listen();
$server->pipe(function (Request $request) {
    return 'HTTP_HOST: ' . $request->header('HTTP_HOST');
});
$server->pipeline()->join($show);
$server->start();