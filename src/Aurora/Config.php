<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora;

use Aurora\Config\Configurable;

abstract class Config implements Configurable
{
    final public function __construct()
    {
        $this->register();
    }

    final public function get($name, $default = false)
    {
        if ( ! isset($this->$name)) return $default;

        return $this->$name;
    }

    final public function __set($name, $value)
    {
        if ( ! isset($this->$name)) {
            throw new Exception("cannot set a unknown attribute");
        }

        $this->$name = $value;
    }

    protected function register()
    {
    }
}