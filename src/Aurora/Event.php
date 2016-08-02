<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora;

use Aurora\Event\EventAccept;
use Aurora\Event\EventAcceptable;

abstract class Event implements EventAcceptable
{
    use EventAccept;
}