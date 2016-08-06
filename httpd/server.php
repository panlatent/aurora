<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

/**
 * 这是一个使用Aurora HTTP (Handler) Framework构建的默认HTTP服务器, 是Aurora默认的工作模式.
 *
 * 将对HTTP Request/Response的管道处理从Aurora类库中分离出来, 能有效的降低Aurora组件耦合性,
 * 有利于其他使用Aurora的程序, 这样处理的逻辑也会十分清晰.
 */
$server = new \Aurora\Http\Server();

$listens = \Aurora\Support\Utils::getSocketBindsFormColonStyle($config->get('bind.listen', '127.0.0.1:10042'));
foreach ($listens as $listen) {
    $server->bind($listen['address'], $listen['port']);
}
$server->listen();
$server->setPipeline(require __DIR__ . '/pipeline.php');

return $server;