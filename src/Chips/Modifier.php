<?php
/**
 * Modifier of cluster endpoints
 * User: moyo
 * Date: 2019-01-21
 * Time: 10:25
 */

namespace Carno\HRPC\Client\Chips;

use Carno\HRPC\Client\Contracts\Modify;
use Carno\HRPC\Client\Endpoints;

trait Modifier
{
    /**
     * @var Modify[]
     */
    private $modifies = [];

    /**
     * @param Modify ...$modifies
     * @return static
     */
    public function modifier(Modify ...$modifies) : self
    {
        array_push($this->modifies, ...$modifies);
        return $this;
    }

    /**
     * @param Endpoints $eps
     * @return Endpoints
     */
    private function modifying(Endpoints $eps) : Endpoints
    {
        foreach ($this->modifies as $modify) {
            $modify->handle($eps);
        }
        return $eps;
    }
}
