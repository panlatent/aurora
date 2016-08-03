<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora\Event;

use Aurora\Support\StringUtil;

trait EventAccept
{
    public function acceptEvent($name, $params = [])
    {
        $methodName = $this->getAcceptEventMethodName($name);

        if ( ! method_exists($this, $methodName) || ! is_callable([$this, $methodName])) {
            return;
        }

        call_user_func_array([$this, $methodName], $params);
    }

    protected function getAcceptEventMethodName($name)
    {
        $nameInfo = $this->getAcceptEventNameInfo($name);

        return 'on' . StringUtil::convertCamel($nameInfo['action']);
    }

    protected function getAcceptEventNameInfo($name)
    {
        if (false === ($pos = strpos($name, ':'))) {
            return ['name' => $name, 'action' => '*'];
        } else {
            return ['name' => substr($name, 0, $pos), 'action' => substr($name, $pos + 1)];
        }
    }
}