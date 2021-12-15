<?php

namespace App\Models;

use JsonSerializable;

class OrderModel implements JsonSerializable
{
    private array $discounts = [];

    public function __construct(
        private $id,
        private $customerId,
        private array $items
    ) {
    }

    public function getTotal(){
        //return $this->total;
        return array_sum(array_column($this->calculateItems(), 'total'));
    }

    public function getCustomerId(){
        return $this->customerId;
    }

    public function addDiscount($discount){
        $this->discounts[] = $discount;
    }

    public function getItems(){
        return $this->items;
    }

    public function calculateItems(){
        $items = [];
        foreach ($this->getItems() as $item) {
            $items[] = array(
                'product_id' => $item['product_id'],
                'category_id' => (float) $item['category_id'],
                'item_price' => (float) $item['price'],
                'quantity' => (float) $item['quantity'],
                'total' => (float) ($item['price'] * $item['quantity'])
            );
        }
        return $items;
    }


    public function updateItems($items){
        return $this->items = $items;
    }

    public function getTotalDiscount(){
        $totalDiscount = 0;
        foreach($this->discounts as $discount){
            $totalDiscount += $discount['discount'];
        }
        return $totalDiscount;
    }

    public function jsonSerialize(){
        return [
            'id' => (int) $this->id,
            'customer-id' => (int) $this->customerId,
            'items' => $this->calculateItems(),
            'sub_total' => (float) $this->getTotal(),
            'total_discount' => (float) $this->getTotalDiscount(),
            'total' => (float) number_format($this->getTotal() - $this->getTotalDiscount(), 2),
            'discounts' => $this->discounts,
        ];
    }
}