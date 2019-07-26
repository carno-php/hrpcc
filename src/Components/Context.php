<?php
/**
 * HTTP RPC context sharing
 * User: moyo
 * Date: 19/03/2018
 * Time: 9:43 PM
 */

namespace Carno\HRPC\Client\Components;

use Carno\Console\Component;
use Carno\Console\Contracts\Application;
use Carno\Console\Contracts\Bootable;
use Carno\Container\DI;
use Carno\HRPC\Client\Handlers\ContextPacking;
use Carno\RPC\Client;
use Carno\RPC\Contracts\Client\Invoker;

class Context extends Component implements Bootable
{
    private const CONF_KEY = 'ctx.trans.allowed';

    /**
     * @var array
     */
    protected $dependencies = [Invoker::class];

    /**
     * @param Application $app
     */
    public function starting(Application $app) : void
    {
        $handler = static function ($value) {
            if (is_null($value)) {
                Client::layers()->remove(ContextPacking::class);
                return;
            }

            $keys = explode('|', $value);

            if (empty($keys)) {
                return;
            }

            /**
             * @var ContextPacking $client
             */

            $client = DI::has(ContextPacking::class)
                ? DI::get(ContextPacking::class)
                : DI::set(ContextPacking::class, new ContextPacking())
            ;

            $client->keys(...$keys);

            Client::layers()->has(ContextPacking::class)
                || Client::layers()->prepend(Invoker::class, $client);
        };

        $app->starting()->add(static function () use ($app, $handler) {
            $app->conf()->watching(self::CONF_KEY, $handler);
        });
    }
}
