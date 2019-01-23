<?php
/**
 * Endpoints modify
 * User: moyo
 * Date: 2019-01-23
 * Time: 10:55
 */

namespace Carno\HRPC\Client\Contracts;

use Carno\HRPC\Client\Endpoints;

interface Modify
{
    /**
     * @param Endpoints $eps
     */
    public function handle(Endpoints $eps) : void;
}
