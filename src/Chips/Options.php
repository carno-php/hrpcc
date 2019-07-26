<?php
/**
 * Options for client dispatcher
 * User: moyo
 * Date: 2018/5/25
 * Time: 11:30 AM
 */

namespace Carno\HRPC\Client\Chips;

use Carno\HTTP\Options as HOptions;
use Carno\Pool\Options as POptions;
use Closure;

trait Options
{
    /**
     * @var Closure
     */
    private $generator = null;

    /**
     * @param Closure $generator
     * @return static
     */
    public function configure(Closure $generator) : self
    {
        $this->generator = $generator;
        return $this;
    }

    /**
     * @param string $server
     * @return HOptions
     */
    private function options(string $server) : HOptions
    {
        if ($this->generator) {
            return ($this->generator)($server);
        } else {
            return (new HOptions())->setTimeouts()->keepalive(new POptions(), "rpc:{$server}");
        }
    }
}
