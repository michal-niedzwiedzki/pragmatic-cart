<?php

namespace Epsi\PragmaticCart\Store;

/**
 * Shopping cart
 *
 * Stores information about products along with their quantities.
 * Can add products to cart, remove products from cart,
 * save cart in persistent storage and load from it by cart id.
 *
 * Cart is persisted in the following JSON structure:
 * {
 *   "id": "int",
 *   "customer": "int",
 *   "products": [
 *     "productId (int)": "quantity (int)",
 *     ...
 *   ]
 * }
 */
final class Cart implements IteratorAggregate {

    const CARTS_DIR = "var/carts/";

    const K_CART_ID = "id";
    const K_CUSTOMER_ID = "customer";
    const K_PRODUCT_QUANTITIES = "products";

    private $id;
    private $customerId;
    private $productQuantities = [];

    private function __construct($id, $customerId) {
        $this->id = $id;
        $this->customerId = $customerId;
    }

    public static function loadByCartId($id) {
        // check if cart file exists
        $file = self::CARTS_DIR . "{%id}.json";
        if (!is_readable($file)) {
            throw new Exception("Could not open file {$file}", Exception::E_CART);
        }

        // check if valid JSON format
        $json = file_get_contents($file);
        $items = json_decode($json, true);
        if (!is_array($items)) {
            throw new Exception("File {$file} does not contain valid JSON array", Exception::E_CART);
        }

        // check if mandatory fields present
        $cartId = $items[self::K_CART_ID];
        $customerId = $items[self::K_CUSTOMER_ID];
        $productQuantities = $items[self::K_PRODUCT_QUANTITIES];

        // start import into cart object
        $cart = new Cart($cartId, $customerId);

        // import product quantities
        foreach ($productQuantities as $productId => $quantity) {
            if (!is_int($productId) or !is_int($quantity)) {
                throw new Exception("Expected int->int mapping, got {$productId}->{$quantity}", Exception::E_CART);
            }
            $cart->productQuantities[$productId] = $quantity;
        }

        return $cart;
    }

    public function save() {
        $file = self::CARTS_DIR . "{$this->id}.json";
        $items = [
            self::K_CART_ID => $this->id,
            self::K_CUSTOMER_ID => $this->customerId,
            self::K_PRODUCT_QUANTITIES => $this->productQuantities,
        ];
        $json = json_encode($items, JSON_PRETTY_PRINT);
        file_put_contents($file, $json);
        return $this;
    }

    public function getId() {
        return $this->id;
    }

    public function getCustomerId() {
        return $this->customerId;
    }

    public function getQuantity($productId) {
        // FIXME nonexistent key handling
        return $this->productQuantities[$productId];
    }

    public function getIterator() {
        return $this->price;
    }

}