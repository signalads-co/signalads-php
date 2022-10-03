<?php

namespace SignalAds\Exceptions;

class ApiException extends BaseRuntimeException
{
	public function getName()
    {
        return 'ApiException';
    }
}

