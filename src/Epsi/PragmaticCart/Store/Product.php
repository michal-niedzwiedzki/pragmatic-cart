<?php

namespace Epsi\PragmaticCart\Store

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

    public static function importFromArray(array $item) {
        return new Product($item["name"], $item["price"], $price["unit"], $price["specialPrice"]);
    }

    public function exportIntoArray() {
        return [
            "name" => $this->name,
            "price" => $this->price,
            "unit" => $this->unitsInBulk,
            "specialPrice" => $this->priceInBulk,
        ];
    }

}