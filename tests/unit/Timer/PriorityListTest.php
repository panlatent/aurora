<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Timer;

use Aurora\Timer\PriorityList;

class PriorityListTest extends \PHPUnit_Framework_TestCase
{
    public function testAccess()
    {
        $priority = new PriorityList();
        $priority->insert('u', 2);
        $priority->insert('a', 6);
        $priority->insert('o', 4);
        $priority->insert('A', 1);
        $priority->insert('r', 5);
        $priority->insert('r', 3);

        $content = '';
        foreach ($priority as $word) {
            $content .= $word;
        }
        $this->assertEquals('Aurora', $content);
    }

}
