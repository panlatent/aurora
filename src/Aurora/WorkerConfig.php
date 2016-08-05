<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora;

use Aurora\Config\FileConfig;

class WorkerConfig extends Config
{
    public $worker_user = 'www-data';

    public $worker_user_group = 'www-data';

    protected function register()
    {
        if ($fileConfig = FileConfig::default()) {
            preg_match('/^(.*?)\s*(|:\s*(.*?))$/', $fileConfig->get('www.user', 'www-data:www-data'), $userMatch);
            $this->worker_user = $userMatch[1];
            $this->worker_user_group = $userMatch[3] ?? '';
        }
    }
}