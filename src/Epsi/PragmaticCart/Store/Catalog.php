<?php

namespace Epsi\PragmaticCart\Store;

/**
 * Products catalog
 *
 * Holds collection of products indexed by product id [int => Product]
 * Can save and load to/from persistent storage.
 *
 * @author Michał Rudnicki <michal@epsi.pl>
 */
final class Catalog {

    /**
     * Collection of products in catalog
     * @var \Epsi\PragmaticCart\Store\Product[]
     */
    private $products = [];

    /**
     * Return all products in catalog
     *
     * @return \Epsi\PragmaticCart\Store\Product[]
     */
    public function getProducts() {
        return $this->products;
    }

    /**
     * Return product by its id
     *
     * @return \Epsi\PragmaticCart\Store\Product
     */
    public function getProductById($productId) {
        if (!isset($this->products[$productId])) {
            throw new Exception("Product {$productId} not in catalog", Exception::E_CATALOG);
        }
        return $this->products[$productId];
    }

    /**
     * Load products from file and return self
     *
     * Will not erase previously loaded products,
     * but may overwrite those with conflicting product id.
     *
     * @return \Epsi\PragmaticCart\Store\Catalog
     */
    public function load($file) {
        // check if catalog file exists
        if (!is_readable($file)) {
            throw new Exception("Could not open {$file}", Exception::E_IMPORT);
        }

        // check if valid JSON format
        $json = file_get_contents($file);
        $products = json_decode($json, true);
        if (!is_array($products)) {
            throw new Exception("File {$file} does not contain valid JSON array", Exception::E_IMPORT);
        }

        // import products
        foreach ($products as $i => $p) {
            if (!is_array($p)) {
                throw new Exception("Array expected at position #{$i} in {$file}", Exception::E_IMPORT);
            }
            $product = Product::import($p);
            $productId = $product->getId();
            $this->products[$productId] = $product;
        }
        return $this;
    }

    /**
     * Save products into file and return self
     *
     * @return \Epsi\PragmaticCart\Store\Catalog
     */
    public function save($file) {
        $products = [];
        foreach ($this->products as $product) {
            $products[] = $product->exportIntoArray();
        }
        $json = json_encode($products, JSON_PRETTY_PRINT);
        file_put_contents($file, $json);
        return $this;
    }

    /**
     * Remove all products from catalog and return self
     *
     * @return \Epsi\PragmaticCart\Store\Catalog
     */
    public function purge() {
        $this->products = [];
        return $this;
    }

}