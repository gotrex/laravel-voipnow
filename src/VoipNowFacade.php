<?php

namespace Gotrex\VoipNow;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Gotrex\VoipNow\SkeletonClass
 */
class VoipNowFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'voipnow';
    }
}
