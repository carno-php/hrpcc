<?php
/**
 * HTTP RPC custom routes
 * User: moyo
 * Date: 2018/4/20
 * Time: 3:23 PM
 */

namespace Carno\HRPC\Client\Components;

use Carno\Console\Component;
use Carno\Console\Contracts\Application;
use Carno\Console\Contracts\Bootable;
use Carno\Container\DI;
use Carno\HRPC\Client\Handlers\RoutesMarking;
use Carno\HRPC\Client\Selector;
use Carno\RPC\Client;
use Carno\RPC\Contracts\Client\Invoker;

class Routes extends Component implements Bootable
{
    /**
     * @var array
     */
    protected $dependencies = [Invoker::class];

    /**
     * @param Application $app
     */
    public function starting(Application $app) : void
    {
        $app->starting()->add(static function () {
            Client::layers()->prepend(Selector::class, DI::object(RoutesMarking::class));
        });
    }
}
