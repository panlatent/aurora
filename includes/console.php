<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

use Aurora\Aurora;
use Aurora\Console\Application;
use Aurora\Console\Command\ServerRestart;
use Aurora\Console\Command\ServerStart;
use Aurora\Console\Command\ServerStatus;
use Aurora\Console\Command\ServerStop;

$application = new Application(Aurora::NAME, Aurora::VERSION);
$application->addCommands([
    new ServerStart(),
    new ServerStop(),
    new ServerStatus(),
    new ServerRestart(),
]);
$application->master(function (\Aurora\Console\Config $config) {
    require __DIR__ . '/server.php';
});
$application->run();