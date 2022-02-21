<?php namespace PlanetaDelEste\ApiOrdersShopaholic\Classes\Event\Order;

use Lovata\OrdersShopaholic\Classes\Collection\OrderCollection;
use Lovata\OrdersShopaholic\Classes\Item\OrderItem;
use Lovata\Toolbox\Classes\Event\ModelHandler;
use Lovata\OrdersShopaholic\Models\Order;
use PlanetaDelEste\ApiOrdersShopaholic\Classes\Store\OrderListStore;
use Lovata\OrdersShopaholic\Classes\Processor\OrderProcessor;
use Sprintsoft\ApiGeneral\Models\ConfiguratorStep;

/**
 * Class OrderModelHandler
 *
 * @package PlanetaDelEste\ApiOrdersShopaholic\Classes\Event\Order
 */
class OrderModelHandler extends ModelHandler
{
    /** @var Order */
    protected $obElement;

    public function subscribe($obEvent)
    {
        parent::subscribe($obEvent);

        OrderCollection::extend(
            function ($obCollection) {
                $this->extendCollection($obCollection);
            }
        );

        $obEvent->listen(OrderProcessor::EVENT_UPDATE_ORDER_AFTER_CREATE, function ($obOrder) {
            $this->sendOrderToPortalAfterCreating($obOrder);
        });
    }

    protected function extendCollection(OrderCollection $obCollection)
    {
        $obCollection->addDynamicMethod(
            'sort',
            function ($sSort = OrderListStore::SORT_CREATED_AT_DESC) use ($obCollection) {
                $arResultIDList = OrderListStore::instance()->sorting->get($sSort);
                return $obCollection->applySorting($arResultIDList);
            }
        );
    }

    /**
     * Get model class name
     *
     * @return string
     */
    protected function getModelClass()
    {
        return Order::class;
    }

    /**
     * Get item class name
     *
     * @return string
     */
    protected function getItemClass()
    {
        return OrderItem::class;
    }

    /**
     * After create event handler
     */
    protected function afterCreate()
    {
        parent::afterCreate();

        $this->clearBySortingPublished();
    }

    /**
     * Clear cache by created_at
     */
    protected function clearBySortingPublished()
    {
        OrderListStore::instance()->sorting->clear(OrderListStore::SORT_CREATED_AT_ASC);
        OrderListStore::instance()->sorting->clear(OrderListStore::SORT_CREATED_AT_DESC);
    }

    /**
     * After save event handler
     */
    protected function afterSave()
    {
        parent::afterSave();
    }

        /**
     * Send order data to portal through API
     */
    protected function sendOrderToPortalAfterCreating($obOrder) {

        $email = $obOrder->getProperty('email');

        // If is fake generated send it empty to portal
        if(strpos($email, 'fake.com') !== FALSE) {
            $email = '';
        }
        
        // Init billing data array
        $arBilling = [
            'first_name'    => $obOrder->getProperty('name'),
            'last_name'     => $obOrder->getProperty('last_name'),
            'company'       => $obOrder->getProperty('name'),
            'cui'           => $obOrder->getProperty('billing_cui'),
            'reg_com'       => $obOrder->getProperty('billing_reg_j'),
            'street'        => $obOrder->getProperty('billing_street'),
            'number'        => $obOrder->getProperty('billing_house'),
            'apartment'     => $obOrder->getProperty('billing_flat'),
            'block'         => $obOrder->getProperty('billing_block'),
            'entrance'      => $obOrder->getProperty('billing_entrance'),
            'city'          => $obOrder->getProperty('billing_city'),
            'county'        => $obOrder->getProperty('billing_state'),
            'postcode'      => $obOrder->getProperty('billing_postcode'),
            'email'         => $email,
            'phone'         => $obOrder->getProperty('phone')
        ];

        // Init shipping data array
        if($obOrder->getProperty('shipping_state')) {
            $arShipping = [
                'first_name'    => $obOrder->getProperty('shipping_name'),
                'last_name'     => $obOrder->getProperty('shipping_last_name'),
                'street'        => $obOrder->getProperty('shipping_street'),
                'number'        => $obOrder->getProperty('shipping_house'),
                'apartment'     => $obOrder->getProperty('shipping_flat'),
                'block'         => $obOrder->getProperty('shipping_block'),
                'entrance'      => $obOrder->getProperty('shipping_entrance'),
                'city'          => $obOrder->getProperty('shipping_city'),
                'county'        => $obOrder->getProperty('shipping_state'),
                'postcode'      => $obOrder->getProperty('shipping_postcode'),
                'phone'         => $obOrder->getProperty('shipping_phone')
            ];
        }

        // Init line items array
        $orderPositions = $obOrder->order_position;

        $enquiry = false;

        foreach ($orderPositions as $orderPosition) {
            $obOffer = $orderPosition->item;
            $obProduct = $obOffer->product;
            $custom_name = $orderPosition->getProperty('custom-name');
            $custom_price = $orderPosition->getProperty('custom-price');
            $custom_quantity = $orderPosition->getProperty('custom-quantity');
            $custom_options = $orderPosition->getProperty('custom-options');
            
            $arLineItems[] = [
                'product_id' => $obProduct->external_id,
                'quantity' => $orderPosition->quantity,
                "custom_name" => $custom_name,
                "custom_price" => $custom_price,
                "custom_quantity" => $custom_quantity,
                "custom_options" => $this->formatOptions($custom_options, $obProduct),
            ];
            if($orderPosition->property) {
                if(isset($orderPosition->property['offer'])) {
                    $enquiry = true;
                }
            }
        }

        // Init Api post data
        $postData = [
            'payment_method' => $obOrder->payment_method ? $obOrder->payment_method->code : '',
            'customer_note' => $obOrder->getProperty('comments'),
            'client_type' => $enquiry ? 'fizica' : $obOrder->getProperty('client_type'),
            'billing' => $arBilling,
            'line_items' => $arLineItems,
            'enquiry' => $enquiry
        ];

        if($obOrder->getProperty('shipping_state')) {
            $postData['shipping'] = $arShipping;
        }

        // Send POST request
        $response = $this->initCurlRequest($postData);

        // On Success replace order_number
        if($response && $response->status == 'success') {
            $orderNumber = $response->details->order_number;

            $obOrder->order_number = $orderNumber;
        } else {
            dd(array('portal_api_error' => $response));
        }
    }

    private function initCurlRequest($postData) {
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => config('api.base_url').'/api/v1/post-order',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($postData),
        CURLOPT_HTTPHEADER => array(
            config('api.key_name').': '.config('api.key'),
            'Content-Type: application/json; charset=utf8'
        ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return json_decode($response);
        }
    }

    /**
     * After delete event handler
     */
    protected function afterDelete()
    {
        parent::afterDelete();

        $this->clearBySortingPublished();
    }

    /**
     * Formatare optiuni pentru functionare corecta in octav
     */
    protected function formatOptions($custom_options, $obProduct) 
    {

        $newCustomOptions = json_decode($custom_options);
        
        // Formatare Optiuni Configurator Cos de fum pentru functionare corecta in Octav
        if(isset($obProduct->property['347']) && $obProduct->property['347'] == 'custom-hoch') {

            $custom_options = json_decode($custom_options);
            $newCustomOptions = [];
            foreach ($custom_options as $option) {

                $stepName = ConfiguratorStep::where('id', $option->step)->value('name');
 
                // Pentru nush ce motiv ultimul sau unul din pasi are value ca array
                if(is_array($option->value)) {
                    $option->value = $option->value[0];
                }

                $stepName = str_replace('/', '-', $stepName);
                $name = str_replace('/', '-', $option->value->name);

                $newCustomOptions[] = [
                    'step_id' => $option->step,
                    'step_name' => $stepName,
                    'id' => $option->value->id,
                    'name' => $name
                ];
            }
            
            $newCustomOptions = ['cos_de_fum' => $newCustomOptions];
        }

        return $newCustomOptions;
    }
}
