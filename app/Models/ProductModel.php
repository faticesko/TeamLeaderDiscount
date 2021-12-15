<?php

namespace App\Models;

class ProductModel
{
//    public function __construct(
//        private $productId,
//        private $categoryId,
//        private $itemPrice,
//        private $quantity,
//        private $total,
//    ) {
//    }

    public function getProducts(){
        return json_decode(file_get_contents( ROOT_DIR .'/var/products.json'), true);
    }


    public function getItemProducts($items)
    {
        $itemIds = array_map(fn (array $item) => $item['product-id'], $items);
        $products = array_filter($this->getProducts(), fn (array $product) => in_array($product['id'], $itemIds));

        $result = [];

        foreach ($products as $product) {
            $item = array_values(array_filter($items, fn ($item) => $item['product-id'] == $product['id']))[0];

            $result[] = array(
                'product_id' => $product['id'],
                'category_id' => $product['category'],
                'price' => $product['price'],
                'quantity' => $item['quantity'],
            );
        }

        return $result;
    }


    static function getCheapestProduct($items){
        $prices = array_column($items, 'price');
        $min_price = min($prices);

        return $min_price;
    }

    


}