<?php namespace PlanetaDelEste\ApiOrdersShopaholic;

use Event;
use PlanetaDelEste\ApiOrdersShopaholic\Classes\Event\ApiShopaholicHandler;
use PlanetaDelEste\ApiOrdersShopaholic\Classes\Event\Order\OrderModelHandler;
use System\Classes\PluginBase;

/**
 * Class Plugin
 * @package PlanetaDelEste\ApiOrdersShopaholic
 */
class Plugin extends PluginBase
{
    const EVENT_ITEMRESOURCE_DATA = 'planetadeleste.apiordersshopaholic.resource.itemData';
    const EVENT_API_ORDER_RESPONSE_DATA = 'planetadeleste.apiordersshopaholic.apiOrderResponseData';
    const EVENT_API_GATEWAY_IPN_RESPONSE = 'planetadeleste.apiordersshopaholic.apiGatewayIpnResponse';

    public $require = [
        'Lovata.OrdersShopaholic',
        'PlanetaDelEste.ApiToolbox'
    ];

    public function boot()
    {
        Event::subscribe(ApiShopaholicHandler::class);
        Event::subscribe(OrderModelHandler::class);
    }
}
