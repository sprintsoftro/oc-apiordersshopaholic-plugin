<?php namespace PlanetaDelEste\ApiOrdersShopaholic\Classes\Resource\OrderPosition;

use PlanetaDelEste\ApiToolbox\Classes\Resource\Base as BaseResource;
use PlanetaDelEste\ApiShopaholic\Classes\Resource\Offer\ItemResource as ItemResourceOffer;

/**
 * Class ItemResource
 *
 * @mixin \Lovata\OrdersShopaholic\Classes\Item\OrderPositionItem
 * @package PlanetaDelEste\ApiOrdersShopaholic\Classes\Resource\OrderPosition
 */
class ItemResource extends BaseResource
{

    /**
     * @inheritDoc
     */
    protected function getEvent(): ?string
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getData(): array
    {
        return [
            'offer' => ItemResourceOffer::make($this->offer)
        ];
    }

    public function getDataKeys(): array
    {
        return [
            'id',
            'order_id',
            'quantity',
            'weight',
            'height',
            'length',
            'width',
        ];
    }
}
