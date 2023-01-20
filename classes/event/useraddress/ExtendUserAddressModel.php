<?php namespace PlanetaDelEste\ApiOrdersShopaholic\Classes\Event\UserAddress;

use Lovata\OrdersShopaholic\Classes\Item\UserAddressItem;
use Lovata\Ordersshopaholic\Models\UserAddress;
use Lovata\Toolbox\Classes\Event\ModelHandler;

/**
 * Class ExtendUserAddressModel
 * @package PlanetaDelEste\ApiOrdersShopaholic\Classes\Event\UserAddress
 */
class ExtendUserAddressModel extends ModelHandler
{
    /** @var UserAddress */
    protected $obElement;

    /**
     * Add listeners
     * @param \Illuminate\Events\Dispatcher $obEvent
     */
    public function subscribe($obEvent)
    {
        parent::subscribe($obEvent);
        
        UserAddress::extend(function ($obUserAddress) {
            /** @var UserAddress $obUserAddress */
            $arFillable = ['company','is_company','cui','reg_number'];
            $obUserAddress->addFillable($arFillable);
            $obUserAddress->addCachedField($arFillable);
        });
    }

    /**
     * Get model class name
     * @return string
     */
    protected function getModelClass()
    {
        return UserAddress::class;
    }

    /**
     * Get item class name
     * @return string
     */
    protected function getItemClass()
    {
        return UserAddressItem::class;
    }
}