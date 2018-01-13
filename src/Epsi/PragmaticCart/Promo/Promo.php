<?php

namespace Epsi\PragmaticCart\Promo;

use \Epsi\PragmaticCart\Checkout\LineItem;
use \Epsi\PragmaticCart\Checkout\Cart;

abstract class Promo {

    /**
     * Promo description to be shown on receipt
     * @var string
     */
    protected $description;

    /**
     * Constructor
     *
     * @param string $description to be shown on receipt
     */
    public function __construct($description) {
        $this->description = $description;
    }

    public function getDescription() {
        return $this->description;
    }

    /**
     * Return discount amount for given line item
     *
     * @param \Epsi\PragmaticCart\Checkout\LineItem $item
     * @return int
     */
    public function getLineItemDiscount(LineItem $item) {
        return 0;
    }

    /**
     * Return discount amount for entire cart
     *
     * @param \Epsi\PragmaticCart\Checkout\Cart $cart to discount
     * @return int
     */
    public function getCartDiscount(Cart $cart) {
        return 0;
    }

}