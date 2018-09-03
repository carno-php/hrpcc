<?php
/**
 * HTTP RPC client init
 * User: moyo
 * Date: 13/12/2017
 * Time: 10:16 AM
 */

namespace Carno\HRPC\Client\Components;

use Carno\Cluster\Resources;
use Carno\Console\Component;
use Carno\Console\Contracts\Application;
use Carno\Console\Contracts\Bootable;
use Carno\Consul\Types\Tagging as SDTags;
use Carno\Container\DI;
use Carno\HRPC\Client\Clustered;
use Carno\HRPC\Client\Contracts\Defined;
use Carno\HRPC\Client\Dispatcher;
use Carno\HRPC\Client\Selector;
use Carno\HRPC\Client\Tagging;
use Carno\HTTP\Options as HOptions;
use Carno\Pool\Options as POptions;
use Carno\RPC\Client;
use Carno\RPC\Contracts\Client\Cluster as ClusterAPI;
use Carno\RPC\Contracts\Client\Invoker as InvokerAPI;

class Invoker extends Component implements Bootable
{
    /**
     * @var array
     */
    protected $dependencies = [Resources::class];

    /**
     * @param Application $app
     */
    public function starting(Application $app) : void
    {
        /**
         * @var SDTags $sdTags
         */
        if (DI::has(SDTags::class) && $sdTags = DI::get(SDTags::class)) {
            $tags = $sdTags->getTags();
        }

        // new global cluster
        $c = new Clustered(DI::get(Resources::class), ...($tags ?? []));

        // custom configure
        $c->configure(static function (string $server) {
            return config()->bind(
                (new HOptions)
                    ->setTimeouts()
                    ->keepalive(config()->bind(new POptions, Defined::OPTS_POOL), "rpc:{$server}"),
                Defined::OPTS_HTTP
            );
        });

        // assign global client implement
        DI::set(ClusterAPI::class, $c);
        DI::set(InvokerAPI::class, $d = DI::object(Dispatcher::class));

        // assign global extensions manager of client
        Client::layers()->append(null, DI::object(Tagging::class), DI::object(Selector::class), $d);
    }
}
