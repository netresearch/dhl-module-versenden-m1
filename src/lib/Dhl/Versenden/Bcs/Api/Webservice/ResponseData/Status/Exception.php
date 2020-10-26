<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\Bcs\Api\Webservice\ResponseData\Status;

use Dhl\Versenden\Bcs\Api\Webservice\ResponseData\Status\Response as ReponseStatus;

class Exception extends \Dhl\Versenden\Bcs\Api\Webservice\Exception
{
    /**
     * Exception constructor.
     * @param ReponseStatus $status
     */
    public function __construct(ReponseStatus $status)
    {
        $messages = $status->getStatusMessage();
        if (!is_array($messages)) {
            $messages = [$messages];
        }

        array_unshift($messages, $status->getStatusText());
        $messages = implode("\n", $messages);
        parent::__construct($messages, $status->getStatusCode());
    }
}
