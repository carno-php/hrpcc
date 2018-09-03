<?php
/**
 * Target endpoints
 * User: moyo
 * Date: 30/10/2017
 * Time: 3:12 PM
 */

namespace Carno\HRPC\Client;

use Carno\Cluster\Managed;
use Carno\HTTP\Client;
use Carno\HTTP\Options;
use Carno\Net\Endpoint;
use Carno\Promise\Promised;

class Endpoints extends Managed
{
    /**
     * @var bool
     */
    protected $strict = false;

    /**
     * @var string
     */
    protected $type = 'rpc';

    /**
     * @var Options
     */
    private $options = null;

    /**
     * Clustered constructor.
     * @param string $server
     * @param array $tags
     * @param Options $options
     */
    public function __construct(string $server, array $tags, Options $options)
    {
        $this->tags = $tags;
        $this->server = $server;
        $this->options = $options;
    }

    /**
     * @param string ...$tags
     * @return Client
     */
    public function select(string ...$tags)
    {
        return $this->picking(...$tags);
    }

    /**
     * @param Endpoint $endpoint
     * @return Client
     */
    protected function connecting(Endpoint $endpoint)
    {
        return new Client($this->options, $endpoint->address());
    }

    /**
     * @param Client $connected
     * @return Promised
     */
    protected function disconnecting($connected) : Promised
    {
        return $connected->close();
    }
}
