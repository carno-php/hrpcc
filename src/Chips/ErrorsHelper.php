<?php
/**
 * HTTP errors
 * User: moyo
 * Date: 10/10/2017
 * Time: 10:51 AM
 */

namespace Carno\HRPC\Client\Chips;

use Carno\HRPC\Client\Contracts\Defined;
use Carno\HTTP\Standard\Message;

trait ErrorsHelper
{
    /**
     * @var int
     */
    private $hDefaultCode = 10000;

    /**
     * @var string
     */
    private $hDefaultMessage = 'Server error';

    /**
     * @param string $message
     * @param int $code
     * @return array
     */
    private function errorHeaders(string $message = null, int $code = null)
    {
        return [
            Defined::X_ERR_CODE => $code ?? $this->hDefaultCode,
            Defined::X_ERR_MESSAGE => $message ?? $this->hDefaultMessage,
        ];
    }

    /**
     * @param Message $http
     * @return bool
     */
    private function errorHappened(Message $http) : bool
    {
        return $http->hasHeader(Defined::X_ERR_CODE) || $http->hasHeader(Defined::X_ERR_MESSAGE);
    }

    /**
     * @param Message $http
     * @return array
     */
    private function errorAsParams(Message $http) : array
    {
        return [
            $http->getHeaderLine(Defined::X_ERR_CODE) ?: $this->hDefaultCode,
            $http->getHeaderLine(Defined::X_ERR_MESSAGE) ?: $this->hDefaultMessage,
        ];
    }
}
