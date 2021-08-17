<?php namespace PlanetaDelEste\ApiOrdersShopaholic\Controllers\Api;

use Kharanenka\Helper\Result;
use Lovata\OrdersShopaholic\Classes\Item\ShippingTypeItem;
use Lovata\OrdersShopaholic\Components\Cart as CartComponent;
use PlanetaDelEste\ApiShopaholic\Classes\Resource\Offer\ShowResource as ShowResourceOffer;
use PlanetaDelEste\ApiShopaholic\Classes\Resource\Product\ItemResource as ItemResourceProduct;
use PlanetaDelEste\ApiToolbox\Classes\Api\Base;

class Cart extends Base
{
    /**
     * @return array
     * @throws \SystemException
     */
    public function getData(): array
    {
        return $this->get();
    }

    /**
     * @return array
     * @throws \SystemException
     * @throws \Exception
     */
    public function add(): array
    {
        $response = $this->cartComponent()->onAdd();
        if (!input('return_data')) {
            return $this->get();
        }

        return $response;
    }

    /**
     * @param int $id
     *
     * @return array
     * @throws \SystemException
     */
    public function update($id = null): array
    {
        $response = $this->cartComponent()->onUpdate();
        if (!input('return_data')) {
            return $this->get();
        }

        return $response;
    }

    /**
     * @return array|\Lovata\Toolbox\Classes\Item\ElementItem[]
     * @throws \SystemException
     */
    public function remove(): array
    {
        $response = $this->cartComponent()->onRemove();
        if (!input('return_data')) {
            return $this->get();
        }

        return $response;
    }

    /**
     * @param int|null $iShippingTypeId
     *
     * @return array|\Lovata\Toolbox\Classes\Item\ElementItem[]
     * @throws \SystemException
     * @throws \Exception
     */
    public function get($iShippingTypeId = null): array
    {
        $obShippingTypeItem = $iShippingTypeId ? ShippingTypeItem::make($iShippingTypeId) : null;
        $obCartPositionCollection = $this->cartComponent()->get($obShippingTypeItem);
        $arCartData = [];
        if ($obCartPositionCollection->isNotEmpty()) {
            $arCartDataPositions = [];
            foreach ($obCartPositionCollection as $obCartPositionItem) {
                /** @var \Lovata\OrdersShopaholic\Classes\Item\CartPositionItem $obCartPositionItem */
                /** @var \Lovata\Shopaholic\Models\Offer $obOfferModel */
                // dd($obCartPositionItem);

                $obOffer = $obCartPositionItem->offer;
//                $obOfferModel = $obOffer->getObject();
                $arCartDataPositions[] = [
                    'offer'                => ShowResourceOffer::make($obOffer),
                    'product'              => ItemResourceProduct::make($obOffer->product),
                    'price_per_unit'       => $obCartPositionItem->price_per_unit,
                    'price_per_unit_value' => $obCartPositionItem->price_per_unit_value,
                    'price_per_unit_without_tax'    => $obCartPositionItem->price_per_unit_without_tax,
                    'price_per_unit_without_tax_value'  => $obCartPositionItem->price_per_unit_without_tax_value,
                    'total'                => $obCartPositionItem->price,
                    'total_value'          => $obCartPositionItem->price_value,
                    'price_without_tax'     => $obCartPositionItem->price_without_tax,
                    'price_without_tax_value'     => $obCartPositionItem->price_without_tax_value,
                    'quantity'             => $obCartPositionItem->quantity,
                    'currency'             => $obOffer->currency,
                ];
            }

            $arCartData = [
                'positions'   => $arCartDataPositions,
                'currency'    => $obCartPositionCollection->getCurrency(),
                'total'       => $obCartPositionCollection->getTotalPrice(),
                'total_value' => $obCartPositionCollection->getTotalPriceValue(),
                'total_price' => $this->cartComponent()->onGetData()['total_price'],
            ];
        }

        return Result::setData($arCartData)->get();
    }

    /**
     * @return \Cms\Classes\ComponentBase|\Lovata\OrdersShopaholic\Components\Cart
     * @throws \SystemException
     */
    protected function cartComponent()
    {
        return $this->component(CartComponent::class);
    }
}
