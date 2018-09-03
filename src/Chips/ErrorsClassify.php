<?php
/**
 * Exceptions classify
 * User: moyo
 * Date: 01/02/2018
 * Time: 4:53 PM
 */

namespace Carno\HRPC\Client\Chips;

use Carno\RPC\Errors\GenericError;
use Carno\RPC\Exception\RemoteLogicException;
use Throwable;

trait ErrorsClassify
{
    /**
     * @param Throwable $e
     * @param bool $acceptLogic
     * @return bool
     */
    protected function isGenericException(Throwable $e, bool $acceptLogic = false) : bool
    {
        return $e instanceof GenericError && ($acceptLogic || !$e instanceof RemoteLogicException);
    }
}
