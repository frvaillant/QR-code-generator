<?php
namespace App\Router\Traits;

trait PlurialTrait
{

    function plurial(array $array, string $plurial = 's', string $singular = '')
    {
        return count($array) > 1 ? $plurial : $singular;
    }

}