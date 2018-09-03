<?php
/**
 * User agent info
 * User: moyo
 * Date: 2018/7/23
 * Time: 2:46 PM
 */

namespace Carno\HRPC\Client;

class Agent
{
    /**
     * @var string
     */
    private $present = null;

    /**
     * Agent constructor.
     */
    public function __construct()
    {
        $this->present = sprintf(
            'Carno-RPC/1.0 (%s %s; PHP %s) SWOOLE/%s',
            php_uname('s'),
            php_uname('r'),
            PHP_VERSION,
            SWOOLE_VERSION
        );
    }

    /**
     * @return string
     */
    public function info() : string
    {
        return $this->present;
    }
}
