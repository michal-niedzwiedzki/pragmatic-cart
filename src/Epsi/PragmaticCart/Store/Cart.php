<?php

namespace Epsi\PragmaticCart\Store;

/**
 * Shopping cart
 *
 * Stores information about products along with their quantities.
 * Aggregates iterator for looping over productId->quantity array.
 * Can add products to cart, remove products from cart,
 * save cart in persistent storage and load from it by cart id.
 *
 * Cart is persisted as simple int->int mapping in JSON.
 * Upon instantiation loading from persistent storage is attempted.
 *
 * @author Michał Rudnicki <michal@epsi.pl>
 */
final class Cart implements IteratorAggregate {

    const CARTS_DIR = "var/carts/";

    private $id;
    private $productQuantities = [];

    public function __construct($id) {
        $this->id = $id;
        $file = self::CARTS_DIR . "{$this->id}.json";
        is_readable($file) and $this->load();
    }

    public function getId() {
        return $this->id;
    }

    public function getIterator() {
        return new ArrayIterator(new ArrayObject($this->productQuantities));
    }

    public function add(Product $product, $quantity) {
        $productId = $product->getId();
        if (isset($this->productQuantities[$productId])) {
            $this->productQuantities[$productId] += $quantity;
        } else {
            $this->productQuantities[$productId] = $quantity;
        }
        return $this;
    }

    public function remove(Product $product, $quantity) {
        $productId = $product->getId();
        if (isset($this->productQuantities[$productId]) and $this->productQuantities[$productId] > $quantity) {
            $this->productQuantities[$productId] -= $quantity;
        } else {
            unset($this->productQuantities[$productId]);
        }
        return $this;
    }

    public function load() {
        // check if cart file exists
        $file = self::CARTS_DIR . "{$this->id}.json";
        if (!is_readable($file)) {
            throw new Exception("Could not open file {$file}", Exception::E_CART);
        }

        // check if valid JSON format
        $json = file_get_contents($file);
        $items = json_decode($json, true);
        if (!is_array($items)) {
            throw new Exception("File {$file} does not contain valid JSON array", Exception::E_CART);
        }

        // import product quantities
        foreach ($items as $productId => $quantity) {
            if (!is_int($productId) or !is_int($quantity)) {
                throw new Exception("Expected int->int mapping, got {$productId}->{$quantity}", Exception::E_CART);
            }
            $cart->productQuantities[$productId] = $quantity;
        }

        return $this;
    }

    public function save() {
        $file = self::CARTS_DIR . "{$this->id}.json";
        $json = json_encode($this->productQuantities, JSON_PRETTY_PRINT);
        file_put_contents($file, $json);
        return $this;
    }

}