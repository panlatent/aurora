<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora;

use Aurora\Timer\TimestampType;

class ServerTimestampType extends TimestampType
{
    const ServerStart = 0;
    const ServerStop = 1;
    const WorkerStart = 100;
    const ClientStart = 200;
    const SocketFirstRead = 300;
    const SocketLastRead = 301;
}