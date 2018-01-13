<?php

namespace Epsi\PragmaticCart\Promo;

use \Epsi\PragmaticCart\Checkout\Cart;
use \Epsi\PragmaticCart\Store\Product;

/**
 *
 *
 * @author Michał Rudnicki <michal@epsi.pl>
 */
final class BundleDiscount extends Promo {

    /**
     * Bundled products
     * @var \Epsi\PragmaticCart\Store\Product[]
     */
    private $products;

    /**
     * Discount amount
     * @var int
     */
    private $discount;

    /**
     * Constructor
     *
     * @param string $description to be shown on receipt
     * @param \Epsi\PragmaticCart\Store\Product[] $bundle of products to be discounted
     * @param int $discount discount to apply
     */
    public function __construct($description, array $products, $discount) {
        parent::__construct($description);
        $this->products = $products;
        $this->discount = $discount;
    }

    /**
     * Return discount amount for entire cart
     *
     * @param \Epsi\PragmaticCart\Checkout\Cart $cart to discount
     * @return int
     */
    public function getCartDiscount(Cart $cart) {
        // check quantities of each product in bundle
        $quantities = [];
        foreach ($this->products as $product) {
            $productId = $product->getId();
            $quantities[$productId] = $cart->getQuantityOf($product);
        }

        // calculate discount to the smallest quantity
        $quantityInPromo = min($quantities);
        return $quantityInPromo * $this->discount;
    }

}