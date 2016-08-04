<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora\Timer;

class MaxHeap extends \SplHeap
{
    protected function compare($value1, $value2)
    {
        if ($value1[0] === $value2[0]) {
            return 0;
        }

        return $value1[0] > $value2[0] ? -1 : 1;
    }

    public function wrap($value, $priority)
    {
        return [$priority, $value];
    }

    public function unwrap($wrap)
    {
        return $wrap[1];
    }
}