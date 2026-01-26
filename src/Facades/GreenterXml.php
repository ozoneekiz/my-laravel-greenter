<?php

namespace CodersFree\LaravelGreenter\Facades;

use Illuminate\Support\Facades\Facade;

class GreenterXml extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'greenter.xml';
    }
}