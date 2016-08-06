<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora\Timer;

abstract class TimestampType
{
    protected $value;

    final public function __construct($value)
    {
       if ( ! in_array($value, array_values(static::constants()))) {
           throw new Exception("Value not is a const in " . get_called_class());
       }

       $this->value = $value;
    }

    public static function constants()
    {
        $class = new \ReflectionClass(get_called_class());

        return $class->getConstants();
    }

    public function getName()
    {
        return array_search($this->value, static::constants());
    }

    public function getValue()
    {
        return $this->value;
    }

}