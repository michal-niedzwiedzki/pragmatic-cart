<?php

namespace Epsi\PragmaticCart\Promo;

use \Epsi\PragmaticCart\Checkout\Cart;

/**
 *
 *
 * @author Michał Rudnicki <michal@epsi.pl>
 */
final class PercentDiscount extends Promo {

    /**
     * Threshold amount for promo to be applicable
     * @var int
     */
    private $threshold;

    /**
     * Discount percent in 1/100th units
     * @var int
     */
    private $percent;

    /**
     * Constructor
     *
     * @param string $description to be shown on receipt
     * @param int $threshold amount for promo to be applicable
     * @param int $percent discount to apply
     */
    public function __construct($description, $threshold, $percent) {
        parent::__construct($description);
        $this->threshold = $threshold;
        $this->percent = $percent;
    }

    /**
     * Return promo description
     *
     * @return string
     */
    public function getDescription() {
        return "{$this->description} ({$this->percent}% off)";
    }

    /**
     * Return discount amount for entire cart
     *
     * @param \Epsi\PragmaticCart\Checkout\Cart $cart to discount
     * @return int
     */
    public function getCartDiscount(Cart $cart) {
        // check if cart acmount over threshold
        $amount = $cart->getAmount();
        if ($amount < $this->threshold) {
            return 0;
        }

        // apply discount
        return $amount * $this->percent / 100;
    }

}