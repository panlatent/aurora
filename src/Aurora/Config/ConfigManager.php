<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora\Config;

trait ConfigManager
{
    protected $config;

    public function config()
    {
        return $this->config;
    }

    public function setConfig(Configurable $config)
    {
        $this->config = $config;
    }
}