<?php namespace PlanetaDelEste\ApiOrdersShopaholic\Classes\Resource\Order;

/**
 * Class ListCollection
 *
 * @package PlanetaDelEste\ApiOrdersShopaholic\Classes\Resource\Order
 */
class ListCollection extends IndexCollection
{
    public $collects = ItemResource::class;
}
