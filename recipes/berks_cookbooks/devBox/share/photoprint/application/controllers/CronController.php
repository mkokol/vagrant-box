<?php

use Elasticsearch\ClientBuilder;

class CronController extends Helpers_General_ControllerAction
{
    public function sendEmailAction()
    {
    }

    public function reindexSearchOldAction()
    {
        $searchContent = new SearchContent();
        $searchContent->reindex();

        exit;
    }

    public function reindexElasticAction()
    {
        $productsItems = new ProductsItems();
        $products = $productsItems->getAllItemsSeoInfo();

        $client = \Elasticsearch\ClientBuilder::create()->build();

//        if ($client->indices()->exists(['index' => 'photoprint'])) {
//            $client->indices()->delete([
//                'index' => 'photoprint'
//            ]);
//        }

//        $client->index([
//            'index' => 'photoprint',
//            'type' => 'search',
//            'body' => ['testField' => 'abc1']
//        ]);
//        $client->index([
//            'index' => 'photoprint',
//            'type' => 'search',
//            'body' => ['testField' => 'abc2']
//        ]);

//        foreach ($products as $product){
//            $client->index([
//                'index' => 'photoprint',
//                'type' => 'search',
//                'body' => [
//                    'id' => $product['id'],
//                    'ua' => $product['ua'],
//                    'ru' => $product['ru'],
//                ]
//            ]);
//        }

        $response = $client->search([
            'index' => 'photoprint',
            'type'  => 'search',
            'body'  => [
                'query' => [
                    'match' => [
                        'testField' => 'abc1'
                    ]
                ]
            ]
        ]);
        echo "<pre>";
        var_dump($response['hits']['total']);
        var_dump($response['hits']['hits']);
        echo "</pre>";

        echo "243";

        exit;
    }

    public function reindexOrdersAction()
    {
        echo '<pre>';

        $orders = new Orders();
        $orderList = $orders->fetchAll()->toArray();
        $client = ClientBuilder::create()->build();

        if ($client->indices()->exists(['index' => 'orders'])) {
            $client->indices()->delete([
                'index' => 'orders'
            ]);
            $client->indices()->create([
                'index' => 'orders',
                'body'  => [
                    'mappings' => [
                        'order' => [
                            '_source'    => [
                                'enabled' => true
                            ],
                            'properties' => [
                                'user_id'           => ['type' => 'string'],
                                'language'          => ['type' => 'string'],
                                'user_name'         => ['type' => 'string'],
                                'address'           => ['type' => 'string'],
                                'phone'             => ['type' => 'string'],
                                'shop_code'         => ['type' => 'string'],
                                'come_from'         => ['type' => 'string'],
                                'created_on'        => [
                                    'type'   => 'date',
                                    'format' => 'yyyy-MM-dd HH:mm:ss',
                                    'index'  => 'analyzed'
                                ],
                                'status'            => ['type' => 'string'],
                                'payment_sys'       => ['type' => 'string'],
                                'payment'           => ['type' => 'float'],
                                'dostavka_lustivok' => ['type' => 'string'],
                                'dostavka_else'     => ['type' => 'string'],
                                'users_pay_out_id'  => ['type' => 'string'],
                                'delivery_code'     => ['type' => 'string']
                            ]
                        ]
                    ]
                ]
            ]);
        }

        foreach ($orderList as $order) {
            $client->index([
                'index' => 'orders',
                'type'  => 'order',
                'id'    => $order['id'],
                'body'  => [
                    'user_id'           => $order['user_id'],
                    'language'          => $order['language'],
                    'user_name'         => $order['user_name'],
                    'address'           => $order['address'],
                    'phone'             => $order['phone'],
                    'shop_code'         => $order['shop_code'],
                    'come_from'         => $order['come_from'],
                    'created_on'        => $order['date'],
                    'status'            => $order['status'],
                    'payment_sys'       => $order['payment_sys'],
                    'payment'           => $order['payment'],
                    'dostavka_lustivok' => $order['dostavka_lustivok'],
                    'dostavka_else'     => $order['dostavka_else'],
                    'users_pay_out_id'  => $order['users_pay_out_id'],
                    'delivery_code'     => $order['delivery_code'],
                ]
            ]);
        }

        echo "Done";

        exit;
    }

    public function reindexSearchAction()
    {
        $productsItems = new ProductsItems();
        $productList = $productsItems->getAllItemsSeoInfoNew();

        $products = new Products();
        $productTypes = $products->getProductsGroup();

        $client = \Elasticsearch\ClientBuilder::create()->build();

        if ($client->indices()->exists(['index' => 'products'])) {
            $client->indices()->delete([
                'index' => 'products'
            ]);
            $client->indices()->create([
                'index' => 'products',
                'body'  => [
                    'mappings' => [
                        'product' => [
                            '_source'    => [
                                'enabled' => true
                            ],
                            'properties' => [
                                'product_id' => ['type' => 'integer'],
                                'ru'         => ['type' => 'string'],
                                'ua'         => ['type' => 'string'],
                                'group_id'   => ['type' => 'integer'],
                                'product'    => ['type' => 'string'],
                                'created_on' => [
                                    'type'   => 'date',
                                    'format' => 'yyyy-MM-dd HH:mm:ss',
                                    'index'  => 'analyzed'
                                ]
                            ]
                        ]
                    ]
                ]
            ]);
        }

        foreach ($productList as $product) {
            foreach ($productTypes as $type) {
                if ($type['group_name'] != $product['group_name']) {
                    continue;
                }

                $client->index([
                    'index' => 'products',
                    'type'  => 'product',
                    'body'  => [
                        'product_id' => $product['id'],
                        'ru'         => $product['ru'],
                        'ua'         => $product['ua'],
                        'created_on' => $product['created'],
                        'product'    => $type['name']
                    ]
                ]);
            }
        }

        echo "Done";
        die;
    }
}
