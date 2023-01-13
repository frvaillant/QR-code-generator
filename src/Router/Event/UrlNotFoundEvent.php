<?php

namespace App\Router\Event;

use Symfony\Component\EventDispatcher\EventDispatcher;

class UrlNotFoundEvent extends \Symfony\Contracts\EventDispatcher\Event
{
    public function __construct()
    {

    }

}