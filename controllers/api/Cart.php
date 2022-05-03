<?php namespace PlanetaDelEste\ApiOrdersShopaholic\Controllers\Api;

use Input;
use Kharanenka\Helper\Result;
use Lovata\OrdersShopaholic\Classes\Item\ShippingTypeItem;
use Lovata\OrdersShopaholic\Classes\Item\PaymentMethodItem;
use Lovata\OrdersShopaholic\Components\Cart as CartComponent;
use PlanetaDelEste\ApiShopaholic\Classes\Resource\Offer\ShowResource as ShowResourceOffer;
use PlanetaDelEste\ApiShopaholic\Classes\Resource\Product\ItemResource as ItemResourceProduct;
use PlanetaDelEste\ApiToolbox\Classes\Api\Base;
use Lovata\OrdersShopaholic\Classes\Processor\CartProcessor;
use Lovata\OrdersShopaholic\Classes\Processor\OfferCartPositionProcessor;
use Lovata\OrdersShopaholic\Components\ShippingTypeList as ShippingTypeListComponent;

class Cart extends Base
{
    /**
     * @return array
     * @throws \SystemException
     */
    public function getData($paymentMethodId = null): array
    {
        /** @var ShippingTypeListComponent $obShippingTypeListComponent */
        $obShippingTypeListComponent = $this->component(ShippingTypeListComponent::class);
        $obShippingTypeList = $obShippingTypeListComponent->make()->sort()->active()->available()->first();
        // dd($obShippingTypeList);
        return $this->get($obShippingTypeList->id, $paymentMethodId);
    }

    /**
     * @return array
     * @throws \SystemException
     * @throws \Exception
     */
    public function add(): array
    {

        $arRequestData = Input::get('cart');
        $obCartData = $this->cartComponent()->onGetCartData();
        $arCartProducts = [];
        foreach($obCartData['data']['position'] as $obPosition) {
            if(empty($obPosition['property'])) {
                $obCartProducts[$obPosition['item_id']] = $obPosition;
                $arCartProducts[] = $obPosition['item_id'];
            }
        }
        foreach($arRequestData as $key => $cartItem) {
            if(in_array($cartItem['offer_id'], $arCartProducts)) {
                $arRequestData[$key]['quantity'] += $obCartProducts[$cartItem['offer_id']]['quantity'];
            }
        }
        CartProcessor::instance()->add($arRequestData, OfferCartPositionProcessor::class);
        Result::setData(CartProcessor::instance()->getCartData());

        $response =  Result::get();
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
            return $this->getData();
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
            return $this->getData();
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
    public function get($iShippingTypeId = null, $iPaymentMethodId = null): array
    {
        $obShippingTypeItem = $iShippingTypeId ? ShippingTypeItem::make($iShippingTypeId) : null;
        $obPaymentMethodItem = $iPaymentMethodId ? PaymentMethodItem::make($iPaymentMethodId) : null;
        $obCartPositionCollection = $this->cartComponent()->get($obShippingTypeItem, $obPaymentMethodItem);

        $arCartData = [];
        if ($obCartPositionCollection->isNotEmpty()) {
            $arCartDataPositions = [];
            foreach ($obCartPositionCollection as $obCartPositionItem) {
                /** @var \Lovata\OrdersShopaholic\Classes\Item\CartPositionItem $obCartPositionItem */
                /** @var \Lovata\Shopaholic\Models\Offer $obOfferModel */
                // dd($obCartPositionItem);

                // dd($obCartPositionItem);
                $obOffer = $obCartPositionItem->offer;
//                $obOfferModel = $obOffer->getObject();
                $arCartDataPositions[] = [
                    'id'       => $obCartPositionItem->id,
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
                    'property'             => $obCartPositionItem->property,
                ];
            }

            $arCartDataPrices = $this->cartComponent()->onGetData();

            $arCartData = [
                'positions'         => $arCartDataPositions,
                'currency'          => $obCartPositionCollection->getCurrency(),
                'total'             => $obCartPositionCollection->getTotalPrice(),
                'total_value'       => $obCartPositionCollection->getTotalPriceValue(),
                'quantity'          => $arCartDataPrices['quantity'],
                'total_quantity'    => $arCartDataPrices['total_quantity'],
                'total_price'       => $arCartDataPrices['total_price'],
                'position_total_price'       => $arCartDataPrices['position_total_price'],
                'shipping_price'       => $arCartDataPrices['shipping_price'],
                'shipping_type_id'       => $arCartDataPrices['shipping_type_id'],
                'payment_method_id'       => $arCartDataPrices['payment_method_id'],
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
