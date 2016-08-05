#!/usr/bin/php
<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_connect($socket, '127.0.0.1', 10043);

$i = 0;
while (socket_write($socket, $content = ($i += rand(1, 9)))) {
    echo "Send: $content\n";
    $arrive = '';
    do {
        $segment = socket_read($socket, 1024);
        $arrive .= $segment;
    } while (1024 == strlen($segment));
    echo "Arrive: $arrive\n";
    sleep(1);
}

