<?php
/**
 * Server
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/seven-server
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Server;

class Daemon {

    protected $key;

    protected $shmId;

    public function __construct($daemonFile)
    {
        if (false === ($this->key = ftok($daemonFile, 'c'))) {
            throw new Exception('Daemon process startup failed: ftok() error');
        }
    }

    public function start()
    {
        if ($this->status()) {
            throw new Exception('Daemon process has been running');
        }

        switch (pcntl_fork()) {
            case 0:
                $this->shmId = shmop_open($this->key, 'c', 0660, 4);
                posix_setsid();
                shmop_write($this->shmId, pack('i1', posix_getpid()), 0);
                break;
            case -1:
                throw new Exception('Daemon process startup failed');
            default:
                exit(0);
        }
    }

    public function status()
    {
        if (false === ($this->shmId = @shmop_open($this->key, 'a', 0660, 4))) {
            return false;
        }

        $pid = unpack('i1', shmop_read($this->shmId, 0, 4))[1];

        return posix_getpgid($pid) ? $pid : false;
    }

    public function stop()
    {
        if ( ! ($pid = $this->status())) {
            throw new Exception('Daemon process has been stopped');
        }

        posix_kill($pid, SIGHUP);
        shmop_close($this->shmId);
    }

    public function restart()
    {

    }

}