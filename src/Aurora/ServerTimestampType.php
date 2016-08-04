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
    const WorkerStart = 2;
    const ClientStart = 3;
    const SocketFirstRead = 4;
    const SocketLastRead = 5;
}