<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\CustomerModel;
use App\Models\OrderModel;
use App\Models\ProductModel;
use Rakit\Validation\Validator;

class DiscountController
{
    private const EXTRA_ITEMS_AFTER = 5;
    private const CUSTOMER_REVENUE_OVER = 1000;

    public function __invoke(Request $request, Response $response){
        $data = json_decode($request->getBody(), true);

        $validator = new Validator();
        $validation = $validator->make($data,[
            'id' => 'required',
            'customer-id' => 'required',
            'items' => 'array|required'
        ]);


        $validation->validate();

        if ($validation->fails()) {
            $errors = $validation->errors();
            $response = $response->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(json_encode($errors->firstOfAll()));
            return $response;
        }

        $productsModel = new ProductModel();
        $products = $productsModel->getItemProducts($data['items']);

        $order = new OrderModel(
            $data['id'],
            $data['customer-id'],
            $products
        );

        $this->calculateDiscount($order);

        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode($order));
        return $response;
    }


    public function customerRevenueDiscount(OrderModel $orderModel){
        $customerId = $orderModel->getCustomerId();
        $customerModel = new CustomerModel;
        $customerRevenue = $customerModel->customerRevenue($customerId);

        if($customerRevenue > self::CUSTOMER_REVENUE_OVER){
            $orderModel->addDiscount(array('note' => 'Customer revenue over: '.self::CUSTOMER_REVENUE_OVER, 'discount' => (float) number_format(($orderModel->getTotal() * 0.1),2)));
        }
    }


    public function switchesCategoryDiscount(OrderModel $orderModel){
        $items = $orderModel->getItems();
        $newItems = array();
        $freeItems = 0;
        $discount = 0;
        foreach ($items as $item){
            if($item['category_id'] == 2){
                $itemPrice = $item['price'];
                $freeItems = floor($item['quantity'] / self::EXTRA_ITEMS_AFTER);
                $item['quantity'] += $freeItems;
                $discount = ($itemPrice * $freeItems);

                if($freeItems) {
                    $orderModel->addDiscount(array('note' => $freeItems .' free '. $item['product_id'], 'discount' => $discount));
                }
            }
            $newItems[] = $item;
        }
        $orderModel->updateItems($newItems);
    }


    public function toolsCategoryDiscount(OrderModel $orderModel){
        $items = $orderModel->getItems();
        $orderedTools = 0;

        foreach ($items as $item){
            if($item['category_id'] == 1){
                $orderedTools+=1;
            }
        }

        if($orderedTools >= 2){
            $cheapestProduct = ProductModel::getCheapestProduct($items);
            $orderModel->addDiscount(array('note' => '20% discount cheapest product', 'discount' => (float) number_format($cheapestProduct * 0.2,2)));
        }
    }


    public function calculateDiscount(OrderModel $orderModel){
        $this->customerRevenueDiscount($orderModel);
        $this->switchesCategoryDiscount($orderModel);
        $this->toolsCategoryDiscount($orderModel);
    }


}