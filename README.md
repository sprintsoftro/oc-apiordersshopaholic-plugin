# API Shopaholic
RESTful API for [Lovata.OrdersShopaholic](https://octobercms.com/plugin/lovata-ordersshopaholic) plugins

## Dependencies
This plugin depends on:

- [PlanetaDelEste.ApiToolbox](https://github.com/planetadeleste/oc-api-toolbox)
- [Lovata.OrdersShopaholic](https://octobercms.com/plugin/lovata-ordersshopaholic)

## Installation
To install from the [repository](https://github.com/planetadeleste/oc-ordersshopaholic-api), clone it into `plugins/planetadeleste/ordersshopaholic` and then run `composer update` from your project root in order to pull in the dependencies.

To install it with **Composer**, run `composer require planetadeleste/oc-ordersshopaholic-plugin` from your project root.

## Documentation

### Endpoints

##### Get Cart data

`GET: /api/v1/cart/data`

##### Get Payment Methods List

`GET: /api/v1/cart/payment_method_list`

##### Add offer

`POST: /api/v1/cart/add?{{ params }}`

```json
let params = {
    'cart': [
        {'offer_id': 32, 'quantity': 4},
        {'offer_id': 44, 'quantity': 1},
    ],
    shipping_type_id: 1, // set shipping method
    payment_method_id: 1, // set payment method
    return_data: 1, // will return cart data
}
```

##### Update offers

`POST: /api/v1/cart/add?{{ params }}`

```json
let params = {
    'cart': [
        {
            'offer_id': 32,
            'quantity': 2 // new quantity
        },
        {
            'offer_id': 44,
            'quantity': 2 // new quantity
        },
    ],
    shipping_type_id: 1, // new shipping method
    payment_method_id: 1, // new payment method
    return_data: 1, // will return cart data
}
```

##### Update cart position

`POST: /api/v1/cart/add?{{ params }}`

```json
let params = {
    'cart': [
        {
            'id': 11, //cart position id
            'offer_id': 32,
            'quantity': 2 // new quantity
        },
        {
            'id': 12,  //cart position id
            'offer_id': 44,
            'quantity': 2 // new quantity
        },
    ],
    shipping_type_id: 1, // new shipping method
    payment_method_id: 1, // new payment method
    return_data: 1, // will return cart data
}
```

##### Remove offer

`POST: /api/v1/cart/add?{{ params }}`

```json
let params = {
  'cart': [
    {'offer_id': 32, 'quantity': 4},
    {'offer_id': 44, 'quantity': 1}
  ],
  'shipping_type_id': 4,
  'payment_method_id': 3
};
```

### Usage
Coming soon

### Events
Coming soon

