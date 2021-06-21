<?php namespace PlanetaDelEste\ApiOrdersShopaholic\Controllers\Api;

use Exception;
use Kharanenka\Helper\Result;
use Lovata\OrdersShopaholic\Classes\Item\OrderItem;
use PlanetaDelEste\ApiOrdersShopaholic\Classes\Resource\OrderPosition\IndexCollection as OrderPositionIndexCollection;
use PlanetaDelEste\ApiToolbox\Classes\Api\Base;
use Lovata\OrdersShopaholic\Models\OrderPosition;
use PlanetaDelEste\ApiToolbox\Plugin as ApiToolboxPlugin;

/**
 * Class Positions
 *
 * @package PlanetaDelEste\ApiOrdersShopaholic\Controllers\Api
 */
class Positions extends Base
{
   public function index()
   {
       try {
           $iOrderId = func_get_arg(0);
           if (!$iOrderId) {
               throw new Exception(static::ALERT_RECORD_NOT_FOUND, 403);
           }

           /** @var \Lovata\OrdersShopaholic\Classes\Item\OrderItem $obOrderItem */
           $obOrderItem = OrderItem::make($iOrderId);
           if ($obOrderItem) {
               Result::setTrue();
               Result::setData(OrderPositionIndexCollection::make($obOrderItem->order_position->collect()));
           } else {
               Result::setFalse();
           }

           return Result::get();
       } catch (Exception $ex) {
           return static::exceptionResult($ex);
       }
   }

    public function getModelClass(): string
    {
        return OrderPosition::class;
    }

    public function getSortColumn(): string
    {
        return 'sort';
    }
}
