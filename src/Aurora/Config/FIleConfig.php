<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora\Config;

use Aurora\Config;

class FileConfig implements \ArrayAccess
{
    /**
     * @var \Aurora\Config\FileConfig
     */
    protected static $default;

    protected $values = [];

    public function __construct($pathname, $namespace = '')
    {
        if ( ! static::$default) {
            static::$default = $this;
        }

        if ( ! is_array($config = $this->loadFile($pathname))) {
            throw new Exception("Unable to parse configuration file: $pathname");
        }

        if ('' == $namespace) {
            $this->values = array_merge($this->values, $config);
        } else {
            $this->values[$namespace] = $config;
        }
    }

    public static function create($pathname, $namespace = '')
    {
        static $paths = [];

        if ( ! static::$default) {
            $paths[$pathname] = $namespace;
            return new static($pathname, $namespace);
        } else if ( ! isset($paths[$pathname])) {
            $paths[$pathname] = $namespace;
            static::$default->merge(new static($pathname, $namespace));
        }

        return static::$default;
    }

    /**
     * @return \Aurora\Config\FileConfig
     */
    public static function getDefault()
    {
        return static::$default;
    }

    public function get($name = '', $default = false)
    {
        if ('' == $name) {
            return $this->values;
        }

        $dotNames = $this->parseDotName($name);
        $values = &$this->values;
        foreach ($dotNames as $subName) {
            if (isset($values[$subName])) {
                $values = &$values[$subName];
            } else {
                return $default;
            }
        }

        return $values;
    }

    public function has($name)
    {
        $dotNames = $this->parseDotName($name);
        $values = &$this->values;
        foreach ($dotNames as $subName) {
            if (isset($values[$subName])) {
                $values = &$values[$subName];
            } else {
                return false;
            }
        }

        return true;
    }

    public function set($name, $value)
    {
        $dotNames = $this->parseDotName($name);
        $lastSubName = array_pop($dotNames);
        $values = &$this->values;
        foreach ($dotNames as $subName) {
            if (isset($values[$subName])) {
                $values = &$values[$subName];
            } else {
                $values[$subName] = [];
            }
        }
        $values[$lastSubName] = $value;
    }

    public function remove($name)
    {
        $dotNames = $this->parseDotName($name);
        $values = &$this->values;
        foreach ($dotNames as $subName) {
            if (isset($values[$subName])) {
                $values = &$values[$subName];
            } else {
                throw new Exception("This configuration item name does not exist: $name");
            }
        }

        unset($values);
    }

    public function clear()
    {
        $this->values = [];
    }

    public function merge(FileConfig $config)
    {
        $this->values = array_merge_recursive($this->values, $config->get());
    }

    public function offsetExists($offset)
    {
        return $this->isset($offset);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->unset($offset);
    }

    protected function loadFile($pathname)
    {
        switch ($this->getExtension($pathname)) {
            case 'php':
                $config = @require($pathname);
                break;
            case 'json':
                $config = @json_decode(file_get_contents($pathname), true);
                break;
            case 'ini':
            default:
                $config = @parse_ini_string(file_get_contents($pathname), true);
        }

        return $config;
    }

    protected function getExtension($pathname)
    {
        if (false === ($dotPos = strrpos($pathname, '.'))) {
            return '';
        }

        return substr($pathname, $dotPos + 1);
    }

    protected function parseDotName($name)
    {
        if (false === (strpos($name, '.'))) {
            return [$name];
        }

        return explode('.', trim($name, '.'));
    }
}