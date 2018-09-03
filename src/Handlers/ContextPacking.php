<?php
/**
 * Client packing of attachments
 * User: moyo
 * Date: 19/03/2018
 * Time: 9:53 PM
 */

namespace Carno\HRPC\Client\Handlers;

use Carno\Chain\Layered;
use Carno\Coroutine\Context;
use Carno\RPC\Protocol\Request;
use Throwable;

class ContextPacking implements Layered
{
    public const HTTP_HEADER = 'X-Trans-CTX';

    /**
     * @var array
     */
    protected $keys = [];

    /**
     * @param string ...$allowed
     */
    public function keys(string ...$allowed) : void
    {
        $this->keys = $allowed;
    }

    public function inbound($request, Context $ctx)
    {
        /**
         * @var Request $request
         */

        $attachments = [];

        foreach ($this->keys as $key) {
            $ctx->has($key) && $attachments[$key] = $ctx->get($key);
        }

        if ($attachments) {
            $dat = base64_encode(json_encode($attachments, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
            $request->opsExtra('h-headers', static function (&$headers) use ($dat) {
                $headers[self::HTTP_HEADER] = $dat;
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
