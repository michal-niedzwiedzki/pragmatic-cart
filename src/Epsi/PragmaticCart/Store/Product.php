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

    const K_NAME = "name";
    const K_PRICE = "price";
    const K_UNITS_IN_BULK = "unit";
    const K_PRICE_IN_BULK = "specialPrice";

    private $name;
    private $price;
    private $unitsInBulk;
    private $priceInBulk;

    public function __construct($name, $price, $unitsInBulk = null, $priceInBulk = null) {
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
        return new Product(
            $p[self::K_NAME],
            $p[self::K_PRICE],
            $p[self::K_UNITS_IN_BULK],
            $p[self::K_PRICE_IN_BULK]
        );
    }

    public function export() {
        return [
            self::K_NAME => $this->name,
            self::K_PRICE => $this->price,
            self::K_UNITS_IN_BULK => $this->unitsInBulk,
            self::K_PRICE_IN_BULK => $this->priceInBulk,
        ];
    }

}