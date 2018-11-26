<?php
/**
 * Tracing injector
 * User: moyo
 * Date: 2018/9/5
 * Time: 5:00 PM
 */

namespace Carno\HRPC\Client\Components;

use Carno\Console\Component;
use Carno\Console\Contracts\Application;
use Carno\Console\Contracts\Bootable;
use Carno\Container\DI;
use Carno\HRPC\Client\Handlers\TracedRequesting;
use Carno\HRPC\Client\Selector;
use Carno\RPC\Client;
use Carno\Traced\Contracts\Observer;

class Tracing extends Component implements Bootable
{
    /**
     * @var int
     */
    protected $priority = 51;

    /**
     * @var array
     */
    protected $dependencies = [Observer::class];

    /**
     * @param Application $app
     */
    public function starting(Application $app) : void
    {
        /**
         * @var Observer $platform
         */

        $platform = DI::get(Observer::class);

        $platform->transportable(static function () {
            Client::layers()->has(TracedRequesting::class)
            || Client::layers()->append(Selector::class, DI::object(TracedRequesting::class));
        }, static function () {
            Client::layers()->remove(TracedRequesting::class);
        });
    }
}
