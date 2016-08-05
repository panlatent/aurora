<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
echo "Test first read wait timeout: \n";
$begin = microtime(true);
socket_connect($socket, '127.0.0.1', 10042);
while (socket_read($socket, 1024)) {
    sleep(1);
}
echo " > Time Interval: ", microtime(true) - $begin, "\n";

$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
echo "Test last read wait timeout: \n";
$begin = microtime(true);
socket_connect($socket, '127.0.0.1', 10043);
socket_write($socket, $content = str_repeat(rand(0, 9), rand(1, 10)));
while (socket_read($socket, 1024)) {
    sleep(1);
}
echo " > Time Interval: ", microtime(true) - $begin, "\n";

