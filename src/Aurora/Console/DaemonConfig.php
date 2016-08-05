<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora\Console;

use Aurora\Config;
use Aurora\Config\FileConfig;
use Aurora\Support\Utils;

class DaemonConfig extends Config
{
    public $pid_file_save_path = '';

    public $master_user = 'daemon';

    public $master_user_group = 'daemon';

    public $worker_user = 'www-data';

    public $worker_user_group = 'www-data';

    public $access_log = '/var/log/aurora/access.log';

    public $error_log = '/var/log/aurora/error.log';

    protected function register()
    {
        if ($fileConfig = FileConfig::default()) {
            $this->pid_file_save_path = $fileConfig->get('daemon.pid', '/var/run/aurora.pid');
            $masterUser = Utils::getUserGroupFromColonStyle($fileConfig->get('daemon.user', 'nobody:nobody'));
            $this->master_user = $masterUser['user'];
            $this->master_user_group = $masterUser['group'];

            $workerUser = Utils::getUserGroupFromColonStyle($fileConfig->get('www.user', 'www-data:www-data'));
            $this->worker_user = $workerUser['user'];
            $this->worker_user_group = $workerUser['group'];
        }

    }
}