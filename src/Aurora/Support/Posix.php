<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora\Support;

class Posix
{
    public static function setUser($user, $group = null)
    {
        if (false === ($info = posix_getpwnam($user))) {
            throw new Exception("Does not exist operating system user: $user");
        }
        $uid = $info['uid'];
        if ($group) {
            if (false === ($info = posix_getgrnam($group))) {
                throw new Exception("Does not exist operating system group: $group");
            } else {
                $gid = $info['gid'];
            }
        } else {
            $gid = $info['gid'];
        }

        static::setUid($uid, $gid);
    }

    public static function setUid($uid, $gid)
    {
        if ( ! posix_setgid($gid)) { // 必须先设置GID, 再设置UID
            throw new Exception("Unable to set GID: " . posix_strerror(posix_get_last_error()));
        }
        if ( ! posix_setuid($uid)) {
            throw new Exception("Unable to set UID: " . posix_strerror(posix_get_last_error()));
        }
    }
}