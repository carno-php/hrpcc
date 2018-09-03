<?php
/**
 * Chain layers for RPC client
 * User: moyo
 * Date: 12/12/2017
 * Time: 11:58 AM
 */

namespace Carno\HRPC\Client\Chips;

use function Carno\Coroutine\async;
use Carno\Coroutine\Context;
use Carno\Promise\Promised;
use Carno\RPC\Protocol\Request;
use Carno\RPC\Protocol\Response;
use Throwable;

trait Layers
{
    /**
     * @param Request $request
     * @param Context $ctx
     * @return Promised
     */
    public function inbound($request, Context $ctx) : Promised
    {
        return async(function (Request $request) {
            return $this->call($request);
        }, $ctx, $request);
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
    public function exception(Throwable $e, Context $ctx) : void
    {
        throw $e;
    }
}
