<?php
/**
 * Invoker test
 * User: moyo
 * Date: 2019-04-07
 * Time: 00:01
 */

namespace Carno\HRPC\Client\Tests;

use Carno\Cluster\Resources;
use Carno\Console\App;
use Carno\Console\Contracts\Bootable;
use Carno\Container\DI;
use function Carno\Coroutine\ctx;
use function Carno\Coroutine\go;
use Carno\HRPC\Client\Clustered;
use Carno\HRPC\Client\Selector;
use Carno\HRPC\Client\Tests\Mocked\EndpointsMod;
use Carno\RPC\Contracts\Client\Cluster;
use Carno\RPC\Contracts\Client\Invoker;
use Carno\RPC\Exception\RemoteLogicException;
use Carno\RPC\Exception\RemoteSystemException;
use Carno\RPC\Protocol\Request;
use Carno\RPC\Protocol\Response;
use Carno\Serving\Components\Discovery\Assigning;
use Carno\Serving\Components\Discovery\Classify;
use Carno\Serving\Components\Discovery\Resourced;
use Carno\Serving\Contracts\Options;
use Carno\Serving\Contracts\ScopedConf;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Throwable;

class InvokerTest extends TestCase
{
    private const SERVER = 'ns.g.s';

    public function testInvoke()
    {
        $def = new InputDefinition;
        $def->addOption(new InputOption(
            Options::SERVICE_DISCOVERY,
            null,
            InputOption::VALUE_OPTIONAL,
            '',
            'config'
        ));

        $app = new App;
        $app->inputs(new ArrayInput([], $def));

        $components = CARNO_COMPONENTS_HRPCC;
        array_unshift($components, Classify::class, Assigning::class, Resourced::class);

        foreach ($components as $cc) {
            /**
             * @var Bootable $boot
             */
            $boot = DI::object($cc);
            $boot->runnable() && $boot->starting($app);
        }

        /**
         * @var Resources $resources
         */
        $resources = DI::get(Resources::class);
        $resources->startup();

        config(ScopedConf::SRV)->set(sprintf(':%s', self::SERVER), '127.0.0.1:80 #TEST');

        /**
         * @var Cluster $cluster
         * @var Clustered $cluster
         */
        $cluster = DI::get(Cluster::class);

        $cluster->modifier(new EndpointsMod);
        $cluster->joining(self::SERVER);

        $selector = new Selector($cluster);

        $tester = $this;
        go(static function () use ($tester, $selector) {
            /**
             * @var Invoker $invoker
             */
            $invoker = DI::get(Invoker::class);

            // test normal

            $selector->inbound(
                $req = (new Request(self::SERVER, 'ss', 'rpc'))->setPayload($body = uniqid()),
                yield ctx()
            );
            /**
             * @var Response $resp
             */
            $resp = yield $invoker->call($req);
            $tester->assertEquals($body, $resp->getPayload());

            // test error logic

            $selector->inbound(
                $req = (new Request(self::SERVER, 'error', 'logic'))->setPayload(''),
                yield ctx()
            );
            $ee = null;
            try {
                yield $invoker->call($req);
            } catch (Throwable $e) {
                $ee = get_class($e);
            }
            $tester->assertEquals(RemoteLogicException::class, $ee);

            // test error system

            $selector->inbound(
                $req = (new Request(self::SERVER, 'error', 'system'))->setPayload(''),
                yield ctx()
            );
            $ee = null;
            try {
                yield $invoker->call($req);
            } catch (Throwable $e) {
                $ee = get_class($e);
            }
            $tester->assertEquals(RemoteSystemException::class, $ee);
        });
    }
}
