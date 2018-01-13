<?php

namespace Epsi\PragmaticCart\Promo;

abstract class Promo {

    /**
     * Promo description to be shown on receipt
     * @var string
     */
    protected $description;

    /**
     * Constructor
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
    public function getCartDiscount(Quote $subject) {
        return 0;
    }

}