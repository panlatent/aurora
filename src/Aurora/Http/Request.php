<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora\Http;

use Aurora\Http\Request\FirstLine;
use Aurora\Http\Request\Header;

class Request implements Producible
{
    /**
     * @var \Aurora\Http\Request\FirstLine
     */
    protected $firstLine;

    /**
     * @var \Aurora\Http\Request\Header
     */
    protected $header;

    /**
     * @var \Aurora\Http\ParameterStorage
     */
    protected $query;

    /**
     * @var \Aurora\Http\ParameterStorage
     */
    protected $post;

    /**
     * @var \Aurora\Http\ParameterStorage
     */
    protected $files;

    /**
     * @var \Aurora\Http\ParameterStorage
     */
    protected $server;

    /**
     * @var string
     */
    protected $rawBody;

    public function __construct(FirstLine $firstLine, Header $header = null, ParameterStorage $query = null,
                                ParameterStorage $post = null, ParameterStorage $files = null,
                                ParameterStorage $server = null, $rawBody = '')
    {
        $this->firstLine = $firstLine;
        $this->header = $header;
        $this->query = $query;
        $this->post = $post;
        $this->files = $files;
        $this->server = $server;
        $this->rawBody = $rawBody;

        if ( ! $this->query) $this->parseQuery();
        if ( ! $this->post) $this->parseRawBody();
        if ( ! $this->files) $this->parseFiles();
        if ( ! $this->server) $this->parseServer();
    }

    public static function factory($rawHttpRequest = null)
    {
        if (is_string($rawHttpRequest)) {
            if (false === ($firstLinePos = strpos($rawHttpRequest, "\r\n"))) {

            }
            $rawFirstLine = substr($rawHttpRequest, 0, $firstLinePos);
            $firstLine = FirstLine::factory($rawFirstLine);
            if (false === ($rawHeaderEndPos = strpos($rawHttpRequest, "\r\n\r\n", $firstLinePos + 2))) {

            }
            $rawHeader = substr($rawHttpRequest, $firstLinePos + 2, $rawHeaderEndPos - $firstLinePos - 2);
            $header = Header::factory($rawHeader);

            $httpContentLength =  $header->header('HTTP_CONTENT_LENGTH', null);
            if ( ! $firstLine->isEmptyBody()) {
                $rawBody = substr($rawHttpRequest, $rawHeaderEndPos + 4, $httpContentLength);
            } else {
                $rawBody = '';
            }
        } else {
            $firstLine = $rawHttpRequest['firstLine'];
            $header = $rawHttpRequest['header'];
            $rawBody = $rawHttpRequest['rawBody'];
        }

        return new static($firstLine, $header, null, null, null, null, $rawBody);
    }

    public function method()
    {
        return $this->firstLine->method();
    }

    public function url()
    {
        return $this->firstLine->uri();
    }

    public function uri()
    {
        return $this->firstLine->uri();
    }

    public function version($onlyNumber = false)
    {
        return $this->firstLine->version($onlyNumber);
    }

    public function query($name = null)
    {
        if (null === $name) {
            return $this->query;
        }

        return $this->query[$name] ?? null;
    }

    public function server($name = null)
    {
        if (null === $name) {
            return $this->server;
        }

        return $this->server[$name] ?? null;
    }

    public function header($name, $default = false)
    {
        return $this->header->get($name, $default);
    }

    public function headers()
    {
        return $this->header;
    }

    public function post($name)
    {
        return $this->post[$name];
    }

    public function posts()
    {
        return $this->post;
    }

    public function rawBody()
    {
        return $this->rawBody;
    }

    public function isPermanenceConnection()
    {
        $version = $this->firstLine->version(true);
        if (version_compare($version, '1.0', '<=')) {
            return ('keep-alive' === $this->header->get('HTTP_CONNECTION', false));
        } else {
            return ('close' !== $this->header->get('HTTP_CONNECTION', true));
        }
    }

    protected function parseRawBody()
    {
        if ($this->firstLine->isEmptyBody()) {
            $this->post = new ParameterStorage();
        } else {
            $contentType = $this->header->headerValue('HTTP_CONTENT_TYPE', 'text/plain');
            switch ($contentType) {
                case 'application/json':
                    $post = json_decode($this->rawBody);
                    break;
                case 'application/x-www-form-urlencoded':
                    foreach (explode('&', $this->rawBody) as $item) {
                        $pos = strpos($item, '=');
                        $post[urldecode(substr($item, 0, $pos))] = urldecode(substr($item, $pos + 1));
                    }
                    break;
                case 'multipart/form-data': // @todo
                    break;
                case 'text/xml': // @todo
                    break;
                case 'text/html':
                case 'text/plain':
                default:
                    break;
            }

            if (isset($post)) {
                $this->post = new ParameterStorage($post);
            }
        }
    }

    protected function parseQuery()
    {
        $query = [];
        if ($rawQuery = $this->firstLine->query()) {
            parse_str($rawQuery, $query);
        }

        $this->query = new ParameterStorage($query);
    }

    protected function parseFiles() // @todo
    {
        $this->files = new ParameterStorage();
    }

    protected function parseServer() // @todo
    {
        $this->server = new ParameterStorage();
    }

}