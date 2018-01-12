<?php

namespace Epsi\PragmaticCart\Store;

final class Catalog {

    const PRODUCTS_LIST = "var/catalog.json";

    private static $instance;

    private $products = [];

    private function __construct() {
        $this->importFromFile(self::PRODUCTS_LIST);
    }

    public static function getInstance() {
        self::$instance or self::$instance = new Catalog();
        return self::$instance;
    }

    public function getProducts() {
        return $this->products;
    }

    public function importFromFile($file) {
        if (!is_readable($file)) {
            throw new Exception("Could not open {$file}", Exception::E_IMPORT);
        }
        $json = file_get_contents($file);
        $items = json_decode($json, true);
        if (!is_array($items)) {
            throw new Exception("File {$file} does not contain valid JSON array", Exception::E_IMPORT);
        }
        foreach ($items as $i => $item) {
            if (!is_array($item)) {
                throw new Exception("Array expected at position #{$i} in {$file}", Exception::E_IMPORT);
            }
            $this->products[] = Product::importFromArray($item); // this [] = operator is like .push() in other languages
        }
        return $this;
    }

    public function exportIntoFile($file) {
        $items = [];
        foreach ($this->products as $product) {
            $items[] = $product->exportIntoArray();
        }
        $json = json_encode($items, JSON_PRETTY_PRINT);
        file_put_contents($file);
    }

    public function purge() {
        $this->products = [];
        return $this;
    }

}