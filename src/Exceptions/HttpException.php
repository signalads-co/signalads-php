<?php

namespace SignalAds\Exceptions;

class HttpException extends BaseRuntimeException
{
	public function getName()
    {
        return 'HttpException';
    }	
}

