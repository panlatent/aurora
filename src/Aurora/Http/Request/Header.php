<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora\Http\Request;

use Aurora\Http\ParameterStorage;
use Aurora\Http\Producible;

class Header extends ParameterStorage implements Producible
{
    public static function factory($rawHeaders = null)
    {
        $headers = [];
        foreach (explode("\r\n", $rawHeaders) as $header) {
            if ($header{0} == "\t" || $header{0} == ' ') {
                $headers[key($headers)] = end($headers) . ' ' . trim($header);
            } else {
                $headerParameter = explode(':', $header, 2);
                $headers['HTTP_' . str_replace('-', '_', strtoupper($headerParameter[0]))] = trim($headerParameter[1]);
            }
        }

        return new static($headers);
    }

    public function getHeader($name, $default = false)
    {
        return $this->get($name, $default);
    }

    public function getHeaderValue($name, $default = false)
    {
        if ( ! ($header = $this->get($name, $default))) {
            return $header;
        }

        if (false !== ($pos = strpos($header, ';'))) {
            return substr($header, 0, $pos);
        } else {
            return $header;
        }
    }

    public function getHeaderParameters($name, $default = false)
    {
        if ( ! ($header = $this->get($name, $default))) {
            return $header;
        }

        if (false !== ($pos = strpos($header, ';'))) {
            return substr($header, $pos + 1);
        } else {
            return $header;
        }
    }

    public function getHeaders()
    {
        return $this->all();
    }
}