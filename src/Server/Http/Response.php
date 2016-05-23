<?php
/**
 * Server
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/seven-server
 * @license https://opensource.org/licenses/MIT
 */

namespace Seven\Server\Http;

class Response {

    protected $status;
    protected $version;
    protected $header;
    protected $rawBody;

    protected $statusCodes = [
        '100' => '',
        '200' => 'OK',
        '404' => 'Not Found',
    ];

    public function __construct($status, $version, $header = [], $rawBody = '')
    {
        $this->status = $status;
        $this->version = $version;
        $this->header = $header;
        $this->rawBody = $rawBody;
    }

    public static function new()
    {
        $status = '200 OK';
        $version = 'HTTP/1.1';
        $header = [];
        $body = '';

        return new static($status, $version, $header, $body);
    }

    public function redirect($url)
    {
        $this->status = '302 Temporarily Moved';
        $this->setHeader('Location', rawurlencode($url));

        return $this;
    }

    public function setHeader($name, $value)
    {
        $this->header[$name] = $value;

        return $this;
    }

    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    public function setStatusCode($code, $des = null)
    {
        if (null === $des) {
            $des = $this->statusCodes[$code];
        }

        $this->status = sprintf('%s %s', $code, $des);

        return $this;
    }

    public function setRawBody($content)
    {
        $this->body = $content;

        return $this;
    }

    public function exportRawHttpMessage()
    {
        $firstLine = sprintf('%s %s', $this->version, $this->status);
        if (empty($this->header['CONTENT_TYPE']))
            $header['CONTENT_TYPE'] = 'text/html';
        if (empty($this->header['CONTENT_LENGTH']))
            $header['CONTENT_LENGTH'] = strlen($this->rawBody);

        $rawHeader = '';
        foreach ($this->header as $key => $value) {
            $key = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower($key))));
            $rawHeader .= sprintf("%s: %s\r\n", $key, $value);
        }

        return sprintf("%s\r\n%s\r\n%s", $firstLine, $rawHeader, $this->rawBody);
    }
}