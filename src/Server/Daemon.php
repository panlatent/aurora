<?php
/**
 * Server
 *
 * @author  panlatent@gmail.com
 */

namespace Panlatent\Server;

use Panlatent\EasyShm\Shm;

class Daemon {

    protected static $instance;

    protected $key;

    protected $shm;

    protected $callback;

    public function __construct($daemonFile, $callback)
    {
        static::$instance = $this;

        if (false === ($this->key = ftok($daemonFile, 'c'))) {
            throw new Exception('Daemon process startup failed: ftok() error');
        }

        $this->shm = new Shm($this->key, 4, 0660);
        $this->callback = $callback;
    }

    /**
     * @return \Panlatent\Server\Daemon
     */
    public static function instance()
    {
        return static::$instance;
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

                call_user_func($this->callback);

                exit(0);
            case -1:
                throw new Exception('Daemon process startup failed');
            default:
                break;
        }
    }

    public function status()
    {
        $pid = unpack('i1', $this->shm->read(0, 4))[1];

        return $pid != 0 && posix_getpgid($pid) ? $pid : false;
    }

    public function stop()
    {
        if ( ! ($pid = $this->status())) {
            throw new Exception('Daemon process has been stopped');
        }

        posix_kill($pid, SIGTERM);
        $this->shm->write(0, pack('i1', 0));
    }

}