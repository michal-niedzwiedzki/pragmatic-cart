<?php

namespace Epsi\PragmaticCart\Checkout;

/**
 * Quote
 *
 * Can produce amount, discount and total.
 * Knows available promos and those that are applicabe.
 *
 * @author Michał Rudnicki <michal@epsi.pl>
 */
interface Quote {

    /**
     * Return amount before promos
     *
     * @return int
     */
    public function getAmount();

    /**
     * Return discount attained from applicable promos
     *
     * @return int
     */
    public function getDiscount();

    /**
     * Return amount recudec by discount
     *
     * @return int
     */
    public function getTotal();

    /**
     * Return list of available promos
     *
     * @return \Epsi\PragmaticCart\Promo\Promo[]
     */
    public function getAvailablePromos();

    /**
     * Return list of applicable promos resulting in discount
     *
     * @return \Epsi\PragmaticCart\Promo\Promo[]
     */
    public function getApplicablePromos();

}