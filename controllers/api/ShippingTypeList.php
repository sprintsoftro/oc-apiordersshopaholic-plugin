<?php namespace PlanetaDelEste\ApiOrdersShopaholic\Controllers\Api;

use Cms\Classes\ComponentManager;
use Kharanenka\Helper\Result;
use Lovata\OrdersShopaholic\Components\ShippingTypeList as ShippingTypeListComponent;
use PlanetaDelEste\ApiToolbox\Classes\Api\Base;

class ShippingTypeList extends Base
{
    /**
     * @return array
     * @throws \SystemException
     */
    public function get(): array
    {
        /** @var ShippingTypeListComponent $obShippingTypeListComponent */
        $obShippingTypeListComponent = $this->component(ShippingTypeListComponent::class);
        $obShippingTypeList = $obShippingTypeListComponent->make()->sort()->active();
        
        return Result::setData($obShippingTypeList->toArray())->get();
    }
}
