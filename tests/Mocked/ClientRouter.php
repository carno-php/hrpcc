<?php
/**
 * Client router
 * User: moyo
 * Date: 2019-04-07
 * Time: 01:36
 */

namespace Carno\HRPC\Client\Tests\Mocked;

use Carno\Cluster\Routing\Typed;
use Carno\HRPC\Client\Contracts\Defined;
use Carno\Net\Endpoint;

class ClientRouter implements Typed
{
    private $nodes = [];

    public function picked(string ...$tags) : array
    {
        return $this->nodes;
    }

    public function classify(string $tag, Endpoint $node) : void
    {
        $this->nodes[] = $node->setOptions([
            Defined::HJ_CLIENT => ClientInstance::class,
        ]);
    }

    public function release(Endpoint $node) : void
    {
    }
}
