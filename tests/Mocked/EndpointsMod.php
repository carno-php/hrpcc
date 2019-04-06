<?php
/**
 * Endpoints modify
 * User: moyo
 * Date: 2019-04-07
 * Time: 01:29
 */

namespace Carno\HRPC\Client\Tests\Mocked;

use Carno\HRPC\Client\Contracts\Modify;
use Carno\HRPC\Client\Endpoints;

class EndpointsMod implements Modify
{
    public function handle(Endpoints $eps) : void
    {
        $eps->routing()->typeset()->extend(new ClientRouter);
    }
}
