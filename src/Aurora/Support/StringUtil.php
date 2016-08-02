<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora\Support;

class StringUtil
{
    const RANDOM_POOL = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    /**
     * Convert a string to camel case.
     *
     * @param string $str
     * @param array  $separators
     * @param string $delimiter
     * @return string
     */
    public static function convertCamel($str, $separators = ['_', '-'], $delimiter = '')
    {
        $str = ucwords(str_replace($separators, ' ', $str));

        return str_replace(' ', $delimiter, $str);
    }

    /**
     * Convert a string to snake case.
     *
     * @param string $str
     * @param string $delimiter
     * @return string
     */
    public static function convertSnake($str, $delimiter = '_')
    {
        if (ctype_lower($str)) return $str;

        return strtolower(preg_replace('/(.)(?=[A-Z])/', '$1' . $delimiter, $str));
    }

    /**
     * Make a random string.
     *
     * @param int    $length
     * @param string $pool
     * @return string
     */
    public static function random($length = 6, $pool = self::RANDOM_POOL)
    {
        return substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
    }
}