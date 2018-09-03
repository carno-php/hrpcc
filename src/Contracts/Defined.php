<?php
/**
 * Defined const
 * User: moyo
 * Date: 2018/9/3
 * Time: 11:12 AM
 */

namespace Carno\HRPC\Client\Contracts;

interface Defined
{
    public const X_ERR_CODE = 'X-Err-Code';
    public const X_ERR_MESSAGE = 'X-Err-Message';

    public const X_ROUTE_TAGS = 'X-Route-Tags';

    public const V_TYPE_JSON = 'application/json';
    public const V_TYPE_PROTO = 'application/x-protobuf';

    public const OPTS_POOL = [
        'rpc.client.pool' => [
            'conn.initial' => 'initial',
            'conn.overall' => 'overall',
            'conn.idle.max' => 'maxIdle',
            'conn.idle.min' => 'minIdle',
            'idle.timeout' => 'idleTimeout',
            'idle.check.inv' => 'icInterval',
            'heartbeat.inv' => 'hbInterval',
            'scale.factor' => 'scaleFactor',
            'select.wait.max' => 'getWaitQMax',
            'select.wait.timeout' => 'getWaitTimeout',
        ]
    ];

    public const OPTS_HTTP = [
        'rpc.client.http' => [
            'tt.overall' => 'ttOverall',
            'tt.connect' => 'ttConnect',
            'tt.send' => 'ttSend',
            'tt.wait' => 'ttWait',
        ]
    ];
}
