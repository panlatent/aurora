<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora\Worker;

use Aurora\Worker;

class WorkerManager
{
    protected $workers;

    protected $status;

    public function __construct()
    {
        $this->workers = new \SplObjectStorage();
    }

    public function count()
    {
        return count($this->workers);
    }

    public function add(Worker $worker)
    {
        $this->workers->attach($worker);
    }

    public function remove($pid)
    {
        /** @var \Aurora\Worker $worker */
        foreach ($this->workers as $worker) {
            if ($pid == $worker->pid()) {
                $this->workers->detach($worker);
                break;
            }
        }
    }

    public function kill(Worker $worker)
    {
        posix_kill($worker->pid(), SIGKILL);
        $this->workers->detach($worker);
    }

    public function killAll()
    {
        /** @var \Aurora\Worker $worker */
        foreach ($this->workers as $worker) {
            posix_kill($worker->pid(), SIGKILL);
        }
    }

}