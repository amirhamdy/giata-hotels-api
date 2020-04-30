<?php

namespace GiataHotels;

use Illuminate\Support\Facades\Facade;

class GiataHotelsFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'GiataAPI';
    }
}
