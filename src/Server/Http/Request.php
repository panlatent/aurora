<?php
/**
 * Server
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/seven-server
 * @license https://opensource.org/licenses/MIT
 */

namespace Seven\Server\Http;

class Request {

    protected $method;
    protected $uri;
    protected $version;
    protected $header;
    protected $query;
    protected $post;
    protected $files;
    protected $server;
    protected $rawBody;

    public function __construct($method, $uri, $version, $header = [], $query = [], $post = [], $files = [], $server = [], $rawBody = '')
    {
        $this->method = $method;
        $this->uri = $uri;
        $this->version = $version;
        $this->header = $header;
        $this->query = $query;
        $this->post = $post;
        $this->files = $files;
        $this->server = $server;
        $this->rawBody = $rawBody;
    }

    public static function new($rawHttpMessage)
    {
        if (false !== ($separatePos = strpos($rawHttpMessage, "\r\n\r\n"))) {
            $rawHeader = substr($rawHttpMessage, 0, $separatePos);
            $rawBody = substr($rawHttpMessage, $separatePos + 4);
        } else {
            $rawHeader = $rawHttpMessage;
            $rawBody = '';
        }

        $header = explode("\r\n", $rawHeader);
        $firstLine = explode(' ', $header[0], 3);

        $method = strtoupper($firstLine[0]);
        $url = $firstLine[1];
        $version = $firstLine[2];

        $urlParts = parse_url($url);
        $uri = rawurldecode($urlParts['path']);

        $query = [];
        if ( ! empty($urlParts['query'])) {
            parse_str($urlParts['query'], $query);
        }

        $header = array_slice($header, 1);
        foreach ($header as $key => $item) {
            if ($item{0} == "\t" || $item{0} == ' ') {
                $header[key($header)] = end($header) . ' ' . trim($item);
            } else {
                $item = explode(':', $item, 2);
                $header['HTTP_' . str_replace('-', '_', strtoupper($item[0]))] = trim($item[1]);
                unset($header[$key]);
            }
        }

        $post = [];
        $files = [];
        if (in_array($method, ['POST', 'PUT', 'PATCH'])) {
            if ( ! isset($header['HTTP_CONTENT_TYPE'])) {
                $header['HTTP_CONTENT_TYPE'] = 'text/plain';
            }
            switch ($header['HTTP_CONTENT_TYPE']) {
                case 'application/json':
                    $post = json_decode($rawBody);
                    break;
                case 'application/x-www-form-urlencoded':
                    foreach (explode('&', $rawBody) as $item) {
                        $pos = strpos($item, '=');
                        $post[rawurldecode(substr($item, 0, $pos))] = rawurldecode(substr($item, $pos + 1));
                    }
                    break;
                case 'multipart/form-data':
                    break;
                case 'text/xml':
                    break;
                case 'text/html':
                case 'text/plain':
                default:
                    $post = [];
            }
        }

        return new static($method, $uri, $version, $header, $query, $post, $files, [], $rawBody); //@todo
    }

    public function uri()
    {
        return $this->uri;
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
}