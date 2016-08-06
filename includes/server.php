<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

use Aurora\Http\Pipeline;
use Aurora\Http\Request;
use Aurora\Http\Response;
use Aurora\Http\Server;
use Aurora\Support\Utils;

/** @var \Aurora\Config\FileConfig $config */
$server = new Server();
$listens = Utils::listens($config->get('bind.listen', '127.0.0.1:10042'));
foreach ($listens as $listen) {
    $server->bind($listen['address'], $listen['port']);
}
$server->listen();

$pipeline = Server::createMatchPipeline();
$pipeline->pipe(function(Request $request) {
    $uri = $request->uri() != '/' ? $request->uri() : '/index.html';
    ob_start();
    include __DIR__ . '/../htdocs' . $uri;
    return ob_get_clean();
});
$pipeline->pipe(function() {
    $response = Response::factory();
    $response->setRawBody(yield);
    return $response;
});
$pipeline->pipe(function(Response $response, Pipeline $pipe) {
    $pipe->data('client')->send($response->getContent());
});

$server->setPipeline($pipeline);
$server->start();