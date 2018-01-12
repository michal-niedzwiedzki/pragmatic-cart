<?php

namespace Epsi\PragmaticCart\Promo;

abstract class Promo {

    /**
     * Registry of all promos in use
     * @var \Epsi\PragmaticCart\Promo\Promo[]
     */
    private static $registeredPromos = [];

    /**
     * Constructor
     */
    public function __construct() {
        Promo::$registeredPromos[] = $this;
    }

    /**
     * Return all registered promos
     *
     * @var \Epsi\PragmaticCart\Promo\Promo[]
     */
    public static function getRegisteredPromos() {
        return Promo::$registeredPromos;
    }

    /**
     * Return whether promo is eligible on given product
     *
     * @param \Epsi\PragmaticCart\Checkout\Quote $quote to work on
     * @param \Epsi\PragmaticCart\Store\Product $subject being under price review
     */
    public function isEligibleOnProduct(Quote $quote, Product $subject) {
        return false;
    }

    /**
     * Return whether promo is eligible on entire purchase
     *
     * @param \Epsi\PragmaticCart\Checkout\Quote $quote to work on
     */
    public function isEligibleOnPurchase(Quote $quote, Product $subject) {
        return false;
    }

    /**
     * Return updated total price for line item with given product
     *
     * @param \Epsi\PragmaticCart\Checkout\Quote $quote to work on
     * @param \Epsi\PragmaticCart\Store\Product $subject being under price review
     * @return int
     */
    public function getLineItemTotalForProduct(Quote $quote, Product $subject) {
        throw new Exception("Discount logic not implemented", Exception::E_PROMO);
    }

    /**
     * Return updated grand total price for entire quote
     *
     * @param \Epsi\PragmaticCart\Checkout\Quote $quote to work on
     * @param \Epsi\PragmaticCart\Store\Product $subject being under price review
     * @return int
     */
    public function getGrandTotalForPurchase(Quote $quote, Product $subject) {
        throw new Exception("Discount logic not implemented", Exception::E_PROMO);
    }

}