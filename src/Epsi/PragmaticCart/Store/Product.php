<?php

namespace Epsi\PragmaticCart\Store;

/**
 * Product in store
 *
 * Represents real product available for puchase by customer.
 *
 * @author Michał Rudnicki <michal@epsi.pl>
 */
class Product {

    private $name;
    private $price;
    private $unitsInBulk;
    private $priceInBulk;

    private function __construct($name, $price, $unitsInBulk = null, $priceInBulk = null) {
        $this->name = $name;
        $this->price = $price;
        $this->unitsInBulk = $unitsInBulk;
        $this->priceInBulk = $priceInBulk;
    }

    public function getId() {
        return $this->name; // TODO: implement proper indexing some other day
    }

    public function getName() {
        return $this->name;
    }

    public function getPrice() {
        return $this->price;
    }

    public function getUnitsInBulk() {
        return $this->unitsInBulk;
    }

    public function getPriceInBulk() {
        return $this->priceInBulk;
    }

    public static function import(array $p) {
        return new Product($p["name"], $p["price"], $p["unit"], $p["specialPrice"]);
    }

    public function export() {
        return [
            "name" => $this->name,
            "price" => $this->price,
            "unit" => $this->unitsInBulk,
            "specialPrice" => $this->priceInBulk,
        ];
    }

}