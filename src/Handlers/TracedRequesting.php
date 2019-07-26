<?php
/**
 * Client requests tracing
 * User: moyo
 * Date: 24/11/2017
 * Time: 4:48 PM
 */

namespace Carno\HRPC\Client\Handlers;

use Carno\Chain\Layered;
use Carno\Coroutine\Context;
use Carno\HRPC\Client\Chips\ErrorsClassify;
use Carno\HRPC\Client\Contracts\TRC;
use Carno\HRPC\Client\Selector;
use Carno\HTTP\Client;
use Carno\HTTP\Standard\Helper as PSRHelper;
use Carno\HTTP\Standard\Request as HRequest;
use Carno\RPC\Protocol\Request as RRequest;
use Carno\RPC\Protocol\Response as RResponse;
use Carno\Tracing\Contracts\Platform;
use Carno\Tracing\Contracts\Vars\EXT;
use Carno\Tracing\Contracts\Vars\FMT;
use Carno\Tracing\Contracts\Vars\TAG;
use Carno\Tracing\Standard\Endpoint;
use Carno\Tracing\Utils\SpansCreator;
use Carno\Tracing\Utils\SpansLinker;
use Throwable;

class TracedRequesting implements Layered
{
    use ErrorsClassify;

    use SpansCreator;
    use SpansLinker;

    use PSRHelper;

    /**
     * @var Platform
     */
    private $platform = null;

    /**
     * ClientRequesting constructor.
     * @param Platform $platform
     */
    public function __construct(Platform $platform)
    {
        $this->platform = $platform;
    }

    /**
     * @param RRequest $request
     * @param Context $ctx
     * @return RRequest
     */
    public function inbound($request, Context $ctx) : RRequest
    {
        /**
         * @var Client $cli
         */
        $cli = $request->getExtra(Selector::CLI);

        $http = new HRequest();

        $this->newSpan(
            $ctx,
            sprintf('%s.%s', $request->service(), $request->method()),
            [
                TAG::SPAN_KIND => TAG::SPAN_KIND_RPC_CLIENT,
                EXT::REMOTE_ENDPOINT => new Endpoint($request->server(), $cli->restricted()),
                TAG::ROUTE_TAGS => implode(',', $request->getTags()),
                TRC::TAG_CLI_APT => get_class($cli),
            ],
            [],
            FMT::HTTP_HEADERS,
            $http,
            $this->linkedCTX($this->rootCTX($ctx)),
            $this->platform
        );

        $headers = $this->getHeaderLines($http);

        $request->opsExtra('h-headers', static function (&$exists) use ($headers) {
            $exists = array_merge($exists, $headers);
        });

        return $request;
    }

    /**
     * @param RResponse $response
     * @param Context $ctx
     * @return RResponse
     */
    public function outbound($response, Context $ctx) : RResponse
    {
        $this->closeSpan($ctx);

        return $response;
    }

    /**
     * @param Throwable $e
     * @param Context $ctx
     * @throws Throwable
     */
    public function exception(Throwable $e, Context $ctx) : void
    {
        $this->isGenericException($e, true) ? $this->closeSpan($ctx) : $this->errorSpan($ctx, $e);

        throw $e;
    }
}
