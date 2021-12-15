<?php

namespace App\Models;

use RuntimeException;

class CustomerModel
{

    public function getCustomers(){
        return json_decode(file_get_contents( ROOT_DIR .'/var/customers.json'), true);
    }

    public function findCustomer($customerId){
        $customers = array_values((array_filter($this->getCustomers(), fn (array $customer) => $customer['id'] == $customerId)));

        if ($customers === []) {
            throw new RuntimeException("Customer not found");
        }
        $customer = $customers[0];
        return $customer;
    }

    public function customerRevenue($customerID){
        $customer = $this->findCustomer($customerID);
        return $customer['revenue'];
    }
}