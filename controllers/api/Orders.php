<?php namespace PlanetaDelEste\ApiOrdersShopaholic\Controllers\Api;

use Event;
use Input;
use Exception;
use Kharanenka\Helper\Result;
use Lovata\OrdersShopaholic\Classes\Collection\OrderCollection;
use Lovata\OrdersShopaholic\Components\MakeOrder;
use Lovata\OrdersShopaholic\Components\Cart as CartComponent;
use Lovata\OrdersShopaholic\Models\Order;
use PlanetaDelEste\ApiOrdersShopaholic\Classes\Resource\Order\IndexCollection;
use PlanetaDelEste\ApiOrdersShopaholic\Classes\Resource\Order\ListCollection;
use PlanetaDelEste\ApiOrdersShopaholic\Classes\Resource\Order\ShowResource;
use PlanetaDelEste\ApiOrdersShopaholic\Classes\Store\OrderListStore;
use PlanetaDelEste\ApiOrdersShopaholic\Plugin;
use PlanetaDelEste\ApiOrdersShopaholic\Classes\Resource\OrderPosition\IndexCollection as OrderPositionIndexCollection;
use PlanetaDelEste\ApiToolbox\Classes\Api\Base;
use PlanetaDelEste\ApiToolbox\Plugin as ApiToolboxPlugin;

use Lovata\OrdersShopaholic\Classes\Processor\CartProcessor;
use Lovata\OrdersShopaholic\Classes\Processor\OfferCartPositionProcessor;

use Multiwebinc\Recaptcha\Validators\RecaptchaValidator;
use System\Models\EventLog;

/**
 * Class Orders
 *
 * @property \Lovata\OrdersShopaholic\Classes\Collection\OrderCollection $collection
 * @package PlanetaDelEste\ApiShopaholic\Controllers\Api
 */
class Orders extends Base
{
    public function init()
    {
        $this->bindEvent(
            ApiToolboxPlugin::EVENT_LOCAL_EXTEND_INDEX,
            function (OrderCollection $obCollection) {
                try {
                    $this->currentUser();
                    if (!$this->isBackend()) {
                        $obCollection->user($this->user->id);
                    }
                } catch (Exception $e) {
                    Result::setFalse()->setMessage($e->getMessage());
                    return response()->json(Result::get(), 403);
                }
            }
        );
    }

    /**
     * @return array|\Illuminate\Http\RedirectResponse
     * @throws \SystemException
     * @throws \Exception
     */
    public function create()
    {
        /** @var MakeOrder $obComponent */
        $obComponent = $this->component(MakeOrder::class);
        $obComponent->onCreate();
        $arResponseData = Event::fire(Plugin::EVENT_API_ORDER_RESPONSE_DATA, [Result::data()]);

        if (!empty($arResponseData)) {
            $arResultData = Result::data();
            foreach ($arResponseData as $arData) {
                if (empty($arData) || !is_array($arData)) {
                    continue;
                }
                $arResultData = array_merge($arResultData, $arData);
            }

            Result::setData($arResultData);
        }

        return Result::get();
    }

    /**
     * @return array|\Illuminate\Http\RedirectResponse
     * @throws \SystemException
     * @throws \Exception
     */
    public function fastOrder()
    {

        if(!$this->validateRecaptcha()) {
            return response()->json([
                'error' => 'invalid recaptcha',
            ], 403);
        }

        $obOldCart = $this->cartComponent()->onGetCartData();

        // preluam id-urile pozitiolor actuale
        $arPositionToDelete = [];
        foreach ($obOldCart['data']['position'] as $position) {
            $arPositionToDelete[] = $position['id'];
        }
        // adaugam produsul curent si le stergem pe celelalte
        $this->cartComponent()->onSync();

        // finalizam cosul de cumparaturi
        /** @var MakeOrder $obComponent */
        $obComponent = $this->component(MakeOrder::class);
        $obComponent->onCreate();
        $arResponseData = Event::fire(Plugin::EVENT_API_ORDER_RESPONSE_DATA, [Result::data()]);

        if (!empty($arResponseData)) {
            $arResultData = Result::data();
            foreach ($arResponseData as $arData) {
                if (empty($arData) || !is_array($arData)) {
                    continue;
                }
                $arResultData = array_merge($arResultData, $arData);
            }

            Result::setData($arResultData);
        }
        $responseCart =  Result::get();

        // readaugam produsel in cos
        if(!empty($arPositionToDelete)) {

            CartProcessor::instance()->restore($arPositionToDelete, OfferCartPositionProcessor::class);
            Result::setData(CartProcessor::instance()->getCartData());
        }

        if(!empty($responseCart['data'])) {
            return [
                'message' => 'Cererea dumneavoastra a fost trimisa. Va multumim!',
                'order_id' => $responseCart['data']['id']
            ];
        } else {
            return [
                'message' => $responseCart['message'],
                'order_id' => null
            ];

        }
    }

    public function positions($sValue)
    {
        try {
            $iOrderId = $this->getItemId($sValue);
            if (!$iOrderId) {
                throw new Exception(static::ALERT_RECORD_NOT_FOUND, 403);
            }

            /** @var \Lovata\OrdersShopaholic\Classes\Item\OrderItem $obOrderItem */
            $obOrderItem = $this->getItem($iOrderId);
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

    public function ipn()
    {
        Event::fire(Plugin::EVENT_API_GATEWAY_IPN_RESPONSE, input());
    }

    public function getModelClass(): string
    {
        return Order::class;
    }

    public function getIndexResource(): string
    {
        return IndexCollection::class;
    }

    public function getListResource(): string
    {
        return ListCollection::class;
    }

    public function getShowResource(): string
    {
        return ShowResource::class;
    }

    public function getPrimaryKey(): string
    {
        return $this->isBackend() ? 'id' : 'secret_key';
    }

    public function getSortColumn(): string
    {
        return OrderListStore::SORT_CREATED_AT_DESC;
    }


    /**
     * @return \Cms\Classes\ComponentBase|\Lovata\OrdersShopaholic\Components\Cart
     * @throws \SystemException
     */
    protected function cartComponent()
    {
        return $this->component(CartComponent::class);
    }

    protected function validateRecaptcha() 
    {   
        $postData = Input::get();
        
        if(!isset($postData['token'])) {
            EventLog::add('Post Contact Form HTTP request Failed', 'error', 'recaptcha missing token');
            return false;
        }

        $recaptchaValidator = new RecaptchaValidator();
        return $recaptchaValidator->validate('g-recaptcha-response', $postData['token'], '');
    }
}
