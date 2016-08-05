<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora\Support;

class Utils
{
    public static function listens($content)
    {
        if ( ! preg_match_all('#([\d.]+):(\d+)#', $content, $match)) {
            return false;
        }
        $listens = [];
        foreach ($match[1] as $offset => $address) {
            $listens[] = ['address' => $address, 'port' => $match[2][$offset]];
        }

        return $listens;
    }

    public static function getUserGroupFromColonStyle($string)
    {
        if ( ! preg_match('/^(.*?)\s*(|:\s*(.*?))$/', $string, $userMatch)) {
            return false;
        }

        return ['user' => $userMatch[1], 'group' => $userMatch[3] ?? ''];
    }
}