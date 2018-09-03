<?php
/**
 * Marked of custom routes
 * User: moyo
 * Date: 2018/4/20
 * Time: 3:40 PM
 */

namespace Carno\HRPC\Client\Handlers;

use Carno\Chain\Layered;
use Carno\Cluster\Contracts\Tags;
use Carno\Coroutine\Context;
use Carno\HRPC\Client\Contracts\Defined;
use Carno\RPC\Protocol\Request;
use Throwable;

class RoutesMarking implements Layered
{
    /**
     * flag in ctx
     */
    public const FLAG = 'routes.marked';

    /**
     * @param Request $request
     * @param Context $ctx
     * @return Request
     */
    public function inbound($request, Context $ctx)
    {
        $tags = $ctx->get(self::FLAG) ?? $request->getTags();
        if ($tags !== Tags::DEFAULT) {
            $request->opsExtra('h-headers', static function (&$headers) use ($request, $tags) {
                $request->setTags(...$tags);
                $headers[Defined::X_ROUTE_TAGS] = implode(',', $tags);
            });
        }

        return $request;
    }

    public function outbound($response, Context $ctx)
    {
        return $response;
    }

    public function exception(Throwable $e, Context $ctx)
    {
        throw $e;
    }
}
