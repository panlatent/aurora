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

/** @var \Aurora\Console\Config $config */
$server = new Server();
$listens = Utils::listens($config->get('bind.listen', '127.0.0.1:10042'));
foreach ($listens as $listen) {
    $server->bind($listen['address'], $listen['port']);
}
$server->listen();

$pipeline = Server::createMatchPipeline();
$pipeline
    ->pipe(function(Request $request) {
    ob_start();
    require __DIR__ . '/../htdocs/index.php';
    return ob_get_clean();
})->pipe(function() {
    $response = Response::factory();
    $response->setRawBody('<html><body>' . yield . '</body></html>');
    return $response;
})->pipe(function(Response $response, Pipeline $pipe) {
    $pipe->client()->send($response->getContent());
});

//$responsePipeline =
//$responsePipeline->pipe(function () {
//    $response = Response::factory();
//    $response->setRawBody('<html><body>' . yield . '</body></html>');
//
//    return $response;
//})->pipe(function () {
//
//});

//$pipeline->join($responsePipeline);

$server->setPipeline($pipeline);
$server->start();