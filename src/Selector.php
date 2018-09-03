<?php
/**
 * Endpoint selector
 * User: moyo
 * Date: 2018/7/25
 * Time: 11:52 AM
 */

namespace Carno\HRPC\Client;

use Carno\Chain\Layered;
use Carno\Coroutine\Context;
use Carno\RPC\Contracts\Client\Cluster;
use Carno\RPC\Protocol\Request;
use Carno\RPC\Protocol\Response;
use Throwable;

class Selector implements Layered
{
    /**
     * rpc extra key for selected client
     */
    public const CLI = '~v';

    /**
     * @var Cluster
     */
    private $cluster = null;

    /**
     * Selector constructor.
     * @param Cluster $cluster
     */
    public function __construct(Cluster $cluster)
    {
        $this->cluster = $cluster;
    }

    /**
     * @param Request $request
     * @param Context $ctx
     * @return Request
     */
    public function inbound($request, Context $ctx)
    {
        $request->setExtra(self::CLI, $this->cluster->picking($request->server(), ...$request->getTags()));
        return $request;
    }

    /**
     * @param Response $response
     * @param Context $ctx
     * @return Response
     */
    public function outbound($response, Context $ctx)
    {
        return $response;
    }

    /**
     * @param Throwable $e
     * @param Context $ctx
     * @throws Throwable
     */
    public function exception(Throwable $e, Context $ctx)
    {
        throw $e;
    }
}
