<?php
/**
 * Client instance
 * User: moyo
 * Date: 2019-04-07
 * Time: 01:37
 */

namespace Carno\HRPC\Client\Tests\Mocked;

use Carno\HRPC\Client\Contracts\Defined;
use Carno\HTTP\Standard\Request;
use Carno\HTTP\Standard\Response;

class ClientInstance
{
    public function perform(Request $request)
    {
        switch ($request->getUri()->getPath()) {
            case '/invoke/error/logic':
                return new Response(200, [Defined::X_ERR_CODE => 1001, Defined::X_ERR_MESSAGE => 'logic']);
            case '/invoke/error/system':
                return new Response(500, [Defined::X_ERR_CODE => 1002, Defined::X_ERR_MESSAGE => 'system']);
            default:
                return new Response(200, $request->getHeaders(), (string) $request->getBody());
        }
    }
}
