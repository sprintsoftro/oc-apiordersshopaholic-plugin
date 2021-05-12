<?php namespace PlanetaDelEste\ApiOrdersShopaholic;

use Event;
use PlanetaDelEste\ApiOrdersShopaholic\Classes\Event\ApiShopaholicHandler;
use System\Classes\PluginBase;

/**
 * Class Plugin
 * @package PlanetaDelEste\ApiOrdersShopaholic
 */
class Plugin extends PluginBase
{
    public $require = [
        'Lovata.OrdersShopaholic',
        'PlanetaDelEste.ApiToolbox'
    ];

    public function boot()
    {
        Event::subscribe(ApiShopaholicHandler::class);
    }
}
