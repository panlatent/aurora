<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora\IPC;

class Pipe
{
    const READ_BUFFER_SIZE = 512;

    protected $pathname;

    protected $fpRead;

    protected $fpWrite;

    protected $readBufferSize = self::READ_BUFFER_SIZE;

    public function __construct($pathname, $mod = 0660)
    {
        if ( ! is_file($pathname)) {
            if ( ! posix_mkfifo($pathname, $mod)) {
                // throw
            }
        }

        $this->pathname = $pathname;
    }

    public function read()
    {
        $fp = fopen($this->pathname, 'r');
        $content = '';
        do {
            $buffer = fread($fp, $this->readBufferSize);
            $content .= $buffer;
        } while (strlen($buffer) == $this->readBufferSize);
        fclose($fp);

        return $content;
    }

    public function write($content)
    {
        $fp = fopen($this->pathname, 'w');

        return fwrite($fp, $content);
    }


}