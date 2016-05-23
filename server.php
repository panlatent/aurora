#!/usr/bin/php70
<?php
/**
 * Server
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/seven-server
 * @license https://opensource.org/licenses/MIT
 */
if ('cli' != php_sapi_name()) {
    die('Can only run in CLI mode');
}

require __DIR__ . '/vendor/autoload.php';

$server = new Seven\Server\Http\Server();
$server->bind('127.0.0.1', 10011);
$server->listen();
$server->setOnRequest(function ($request, $response) {
    var_dump($request);
});
$server->start();