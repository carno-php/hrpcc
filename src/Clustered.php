<?php
/**
 * Clustered resources
 * User: moyo
 * Date: 2018/7/25
 * Time: 11:03 AM
 */

namespace Carno\HRPC\Client;

use Carno\Cluster\Classify\Scenes;
use Carno\Cluster\Resources;
use Carno\HRPC\Client\Chips\Modifier;
use Carno\HRPC\Client\Chips\Options;
use Carno\HRPC\Client\Exception\EndpointsNotFoundException;
use Carno\HTTP\Client;
use Carno\RPC\Contracts\Client\Cluster;

class Clustered implements Cluster
{
    use Options, Modifier;

    /**
     * @var Resources
     */
    private $resources = null;

    /**
     * @var Endpoints[]
     */
    private $targets = [];

    /**
     * @var array
     */
    private $tags = [];

    /**
     * Clustered constructor.
     * @param Resources $resources
     * @param string ...$tags
     */
    public function __construct(Resources $resources, string ...$tags)
    {
        $this->resources = $resources;
        $this->tags = $tags;
    }

    /**
     * @return string[]
     */
    public function tags() : array
    {
        return $this->tags;
    }

    /**
     * @param string $server
     */
    public function joining(string $server) : void
    {
        $cluster =
            $this->targets[$server] ??
            $this->targets[$server] = $this->modifying(new Endpoints($server, $this->tags(), $this->options($server)))
        ;

        $this->resources->initialize(Scenes::SERVICE, '', $server, $cluster);
    }

    /**
     * @param string $server
     * @param string ...$tags
     * @return Client
     */
    public function picking(string $server, string ...$tags) : object
    {
        if (is_null($endpoints = $this->targets[$server] ?? null)) {
            throw new EndpointsNotFoundException;
        } else {
            return $endpoints->select(...$tags);
        }
    }
}
