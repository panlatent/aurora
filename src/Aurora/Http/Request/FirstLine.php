<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora\Http\Request;

use Aurora\Http\Exception;
use Aurora\Http\Producible;

class FirstLine implements Producible
{
    const DEFAULT_HTTP_VERSION = 'HTTP/1.1';

    protected static $methodSupports = [
        '1.0' => ['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE'],
        '1.1' => ['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'OPTIONS', 'DELETE'], // Ignore TRACE
        '2.0' => [] // Don't support HTTP/2.0
    ];

    protected static $emptyBodyMethods = [
        'GET', 'HEAD', 'OPTIONS'
    ];

    /**
     * @var string
     */
    protected $version;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $method;

    /*
     * @var string
     */
    protected $uri;

    /**
     * @var string
     */
    protected $query;

    public function __construct($method, $url, $version = '')
    {
        if (empty($version)) $version = static::DEFAULT_HTTP_VERSION;

        if ( ! preg_match('#^HTTP/[0-9]+\.[0-9]+$#', $version)) {
            throw new Exception("Error HTTP version format");
        }
        $this->version = $version;

        if ( ! in_array($method, static::$methodSupports[$this->version(true)])) {
            throw new Exception("This is not supported HTTP method");
        }
        $this->method = $method;
        $this->url = $url;

        $urlParameter = parse_url($url);
        $this->uri = urldecode($urlParameter['path']);
        $this->query = $urlParameter['query'] ?? '';
    }

    public static function factory($rawFirstLine = null)
    {
        $fields = explode(' ', $rawFirstLine, 3);

        return new static($fields[0], $fields[1], $fields[2]);
    }

    public function query()
    {
        return $this->query;
    }

    public function method()
    {
        $this->method;
    }

    public function url()
    {
        return $this->url;
    }

    public function uri()
    {
        return $this->uri;
    }

    public function version($onlyNumber = false)
    {
        if ($onlyNumber) {
            return substr($this->version, 5);
        } else {
            return $this->version;
        }
    }

    public function isEmptyBody()
    {
        if (in_array($this->method, static::$emptyBodyMethods)) {
            return true;
        } else {
            return false;
        }
    }

}