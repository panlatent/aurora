<?php
/**
 * Server
 *
 * @author  panlatent@gmail.com
 */

namespace Panlatent\Server;

use Panlatent\EasyShm\Shm;

class Daemon {

    protected $key;

    protected $shm;

    public function __construct($daemonFile)
    {
        if (false === ($this->key = ftok($daemonFile, 'c'))) {
            throw new Exception('Daemon process startup failed: ftok() error');
        }

        $this->shm = new Shm($this->key, 4, 0660);
    }

    public function __destruct()
    {
        if (false === $this->status()) {
            $this->shm->delete();
        }
    }

    public function status()
    {
        $pid = unpack('i1', $this->shm->read(0, 4))[1];

        return $pid != 0 || posix_getpgid($pid) ? $pid : false;
    }

    public function restart()
    {
        if ($pid = $this->status()) {
            $this->stop();
        }

        $this->start();
    }

    public function stop()
    {
        if ( ! ($pid = $this->status())) {
            throw new Exception('Daemon process has been stopped');
        }

        posix_kill($pid, SIGHUP);
        $this->shm->write(0, pack('i1', 0));
    }

    public function start()
    {
        if ($this->status()) {
            throw new Exception('Daemon process has been running');
        }

        switch (pcntl_fork()) {
            case 0:
                posix_setsid();
                $this->shm->write(0, pack('i1', posix_getpid()));
                break;
            case -1:
                throw new Exception('Daemon process startup failed');
            default:
                exit(0);
        }
    }

}