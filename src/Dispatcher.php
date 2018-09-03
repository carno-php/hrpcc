<?php
/**
 * Client dispatcher
 * User: moyo
 * Date: 30/09/2017
 * Time: 11:04 AM
 */

namespace Carno\HRPC\Client;

use Carno\HRPC\Client\Chips\ErrorsHelper;
use Carno\HRPC\Client\Chips\Layers;
use Carno\HRPC\Client\Contracts\Defined;
use Carno\HTTP\Client;
use Carno\HTTP\Standard\Request as HRequest;
use Carno\HTTP\Standard\Response as HResponse;
use Carno\HTTP\Standard\Streams\Body;
use Carno\HTTP\Standard\Uri;
use Carno\RPC\Contracts\Client\Cluster;
use Carno\RPC\Contracts\Client\Invoker;
use Carno\RPC\Exception\RemoteLogicException;
use Carno\RPC\Exception\RemoteSystemException;
use Carno\RPC\Protocol\Request as RRequest;
use Carno\RPC\Protocol\Response as RResponse;

class Dispatcher implements Invoker
{
    use Layers, ErrorsHelper;

    /**
     * @var Clustered
     */
    private $cluster = null;

    /**
     * @var Agent
     */
    private $agent = null;

    /**
     * Dispatcher constructor.
     * @param Cluster $cluster
     * @param Agent $agent
     */
    public function __construct(Cluster $cluster, Agent $agent)
    {
        $this->cluster = $cluster;
        $this->agent = $agent;
    }

    /**
     * @param RRequest $rpc
     * @return RResponse
     */
    public function call(RRequest $rpc)
    {
        $request = new HRequest(
            'POST',
            new Uri(
                'http',
                $rpc->server(),
                null,
                sprintf('/invoke/%s/%s', $rpc->service(), $rpc->method())
            ),
            array_merge(
                [
                    'Host' => $rpc->server(),
                    'Content-Type' => $rpc->isJsonc() ? Defined::V_TYPE_JSON : Defined::V_TYPE_PROTO,
                    'User-Agent' => $this->agent->info(),
                ],
                $rpc->getExtra('h-headers') ?? []
            ),
            new Body($rpc->getPayload())
        );

        /**
         * @var Client $client
         * @var HResponse $response
         */

        $client = $rpc->getExtra(Selector::CLI);

        $response = yield $client->perform($request);

        if ($this->errorHappened($response)) {
            throw $response->getStatusCode() === 200
                ? new RemoteLogicException(...$this->errorAsParams($response))
                : new RemoteSystemException(...$this->errorAsParams($response))
            ;
        }

        return new RResponse($rpc, (string) $response->getBody());
    }
}
