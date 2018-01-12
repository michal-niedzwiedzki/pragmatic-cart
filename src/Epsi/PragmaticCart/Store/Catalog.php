<?php

namespace Epsi\PragmaticCart\Store;

/**
 * Products catalog
 *
 * A singleton holding a collection of products.
 * Can save and load to/from persistent storage.
 *
 * @author Michał Rudnicki <michal@epsi.pl>
 */
final class Catalog {

    const CATALOG_FILE = "var/catalog.json";

    /**
     * Singleton instance
     * @var \Epsi\PragmaticCart\Store\Catalog
     */
    private static $instance;

    /**
     * Collection of products in catalog
     * @var \Epsi\PragmaticCart\Store\Product[]
     */
    private $products = [];

    /**
     * Singleton constructor
     */
    private function __construct() {

    }

    public static function getInstance() {
        self::$instance or self::$instance = new Catalog();
        return self::$instance;
    }

    public function getProducts() {
        return $this->products;
    }

    public function getProductById($productId) {
        foreach ($this->products as $product) {
            if ($product->getid() == $productId) {
                return $product;
            }
        }
        throw new Exception("Product {$productId} not in catalog", Exception::E_CATALOG);
    }

    public function load($file) {
        // check if catalog file exists
        $file or $file = self::CATALOG_FILE;
        if (!is_readable($file)) {
            throw new Exception("Could not open {$file}", Exception::E_IMPORT);
        }

        // check if valid JSON format
        $json = file_get_contents($file);
        $items = json_decode($json, true);
        if (!is_array($items)) {
            throw new Exception("File {$file} does not contain valid JSON array", Exception::E_IMPORT);
        }

        // import products
        foreach ($items as $i => $item) {
            if (!is_array($item)) {
                throw new Exception("Array expected at position #{$i} in {$file}", Exception::E_IMPORT);
            }
            $product = Product::importFromArray($item);
            $productId = $product->getId();
            $this->products[$productId] = $product;
        }
        return $this;
    }

    public function save($file = null) {
        $file or $file = self::CATALOG_FILE;
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