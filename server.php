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

$cmdOptions = getopt('s::');
$signal = $cmdOptions['s'] ?? 'start';

try {
    $daemon = new \Panlatent\Server\Daemon(__FILE__);
    switch ($signal) {
        case 'start':
            $daemon->start();
            break;
        case 'stop':
            $daemon->stop();
            exit(0);
        case 'status':
            if ($daemon->status()) {
                die('Server is Running');
            } else {
                die('Server is Stopped');
            }
            break;
        default:
            die('Bad command line parameter');
    }
} catch (\Exception $e) {
    die($e->getMessage());
}

$server = new Panlatent\Server\Http\Server();
$server->bind('127.0.0.1', 10011);
$server->listen();
$server->setOnRequest(function ($request, $response) {
    var_dump($request);
});
$server->start();