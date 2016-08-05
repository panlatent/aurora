<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora\Console;

use Aurora\Config\ConfigManageable;
use Aurora\Config\ConfigManager;
use Aurora\Support\Posix;

class Daemon implements ConfigManageable
{
    use ConfigManager;

    public function __construct(DaemonConfig $config = null)
    {
        $this->config = $config ?? new DaemonConfig();
    }

    /**
     * @return \Generator|void
     * @throws \Aurora\Console\Exception
     */
    public function start()
    {
        if (0 < ($pid = pcntl_fork())) {
            return;
        } elseif (-1 == $pid) {
            throw new Exception("Unable to create daemon process");
        }

        posix_setsid();

        if (0 < ($pid = pcntl_fork())) { // 禁止进程重新打开控制终端
            exit(0);
        } elseif (-1 == $pid) {
            throw new Exception("Unable to create the process of leaving the terminal");
        }

        chdir('/');
        umask(0);

        $user = $this->config->get('master_user', 'nobody');
        $group = $this->config->get('master_user_group', '');
        Posix::setUser($user, $group);

        $filename = $this->config->get('pid_file_save_path', '/var/run/aurora.pid');
        if ( ! ($fd = @fopen($filename, 'w+'))) {
            throw new Exception("PID file creation failed" . error_get_last()['message']);
        } elseif ( ! flock($fd, LOCK_EX | LOCK_NB)) {
            throw new Exception("Unable to lock the PID file");
        }
        fwrite($fd, posix_getpid());

        // $this->closeStdDescriptors();
        call_user_func(yield, $this->config);

        flock($fd, LOCK_UN);
        fclose($fd);
        unlink($filename);

        exit(0);
    }

    public function stop()
    {
        if (false === ($pid = $this->getServerPid())) {
            return false;
        }

        return posix_kill($pid, SIGTERM);
    }

    protected function getServerPid()
    {
        static $cachePid = null;

        if (null !== $cachePid) {
            return $cachePid;
        }

        if ( ! is_file($filename = $this->config->get('pid_file_save_path'))) {
            return false;
        }

        if ( ! ($fp = fopen($filename, 'r'))) {
            throw new Exception("Unable to open PID file");
        }

        if (flock($fp, LOCK_EX | LOCK_NB)) {
            flock($fp, LOCK_UN);
            fclose($fp);

            return false;
        }

        $pid = fread($fp, 32);
        fclose($fp);

        return ($cachePid = (int)$pid);
    }

    public function reload()
    {
        // @todo
    }

    public function status()
    {
        if (false === ($pid = $this->getServerPid())) {
            return false;
        } elseif ( ! posix_kill($pid, 0)) {
            return false;
        }

        return $this->getServerStatus($pid);
    }

    protected function getServerStatus($pid)
    {
        // @todo

        return ['pid' => $pid];
    }

    public function closeStdDescriptors()
    {
        fclose(STDOUT);
        fclose(STDIN);
        fclose(STDERR);
    }

}