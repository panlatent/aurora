<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora\Console;

class Application extends \Symfony\Component\Console\Application
{
    protected $childProcessCallback;

    public function getChildProcess()
    {
        return $this->childProcessCallback;
    }

    public function setChildProcess($callback)
    {
        $this->childProcessCallback = $callback;
    }
}