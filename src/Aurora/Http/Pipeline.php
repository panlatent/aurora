<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora\Http;

use Aurora\Event\EventAccept;
use Aurora\Event\EventAcceptable;
use Aurora\Event\EventManageable;
use Aurora\Event\EventManager;
use Aurora\Http\Request\FirstLine;
use Aurora\Http\Request\Header as HttpHeader;

class Pipeline extends \Aurora\Pipeline implements EventAcceptable, EventManageable
{
    use EventAccept, EventManager;

    public function append($content)
    {
        parent::append($content);
        $this->event->fire('pipeline:append');
    }

    public function dispatch($content)
    {
        parent::dispatch($content);
        $this->event->fire('pipeline:dispatch');

        return $this;
    }

    protected function onAppend()
    {
        static $firstLine;
        static $header;

        if ( ! $firstLine) {
            if (false !== ($pos = $this->buffer->find("\r\n"))) {
                $rawFirstLine = $this->buffer->pop($pos, 2);
                $firstLine = FirstLine::factory($rawFirstLine);
            }
        }

        if ($firstLine && ! $header) {
            if (false !== ($pos = $this->buffer->find("\r\n\r\n"))) {
                $rawHeader = $this->buffer->pop($pos, 4);
                $header = HttpHeader::factory($rawHeader);
            }
        }

        if ($header) {
            $httpContentLength =  $header['HTTP_CONTENT_LENGTH'];
            if (($isEmptyBody = $firstLine->isEmptyBody()) || $this->buffer->size() >= $httpContentLength) {
                $rawBody = ! $isEmptyBody ? $this->buffer->pop($httpContentLength) : '';
                $request = Request::factory([
                    'firstLine' => $firstLine,
                    'header' => $header,
                    'rawBody' => $rawBody
                ]);
                $firstLine = null;
                $header = null;

                var_dump($request);

                $this->dispatch($request);
            }
        }
    }
}