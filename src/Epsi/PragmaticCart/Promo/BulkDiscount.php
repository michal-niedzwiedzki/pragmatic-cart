<?php

namespace Epsi\PragmaticCart\Promo;

/**
 * Promo to apply special price if quantity over threshold
 *
 * @author Michał Rudnicki <michal@epsi.pl>
 */
final class BulkDiscount extends Promo {

    /**
     * Target product the promo applies to
     * @var \Epsi\PragmaticCart\Store\Product
     */
    private $target;

    private $exclusive;

    /**
     * Constructor
     *
     * @param \Epsi\PragmaticCart\Store\Product $target product the promo applies to
     * @param boolean $exclusive flag to make the promo only available as the only one on a product
     */
    public function __construct($description, Product $target, $exclusive) {
        parent::__construct($description);
        $this->target = $target;
        $this->exclusive = $exclusive;
    }

    /**
     * Return whether promo is eligible on given product
     *
     * @param \Epsi\PragmaticCart\Checkout\Quote $quote to work on
     * @param \Epsi\PragmaticCart\Store\Product $subject being under price review
     * @return boolean
     */
    public function isEligibleOnProduct(Quote $quote, Product $subject) {
        // target product must match subject
        if ($this->target != $subject) {
            return false;
        }
        // if exclusive no other promo allowed on subject
        if ($this->exclusive and !empty($quote->getPromos($subject))) {
            return false;
        }
        // quantity over threshold
        if ($quote->getQuantity($subject) < $subject->getUnitsInBulk()) {
            return false;
        }
        return true;
    }

    /**
     * Return updated total price for line item with given product
     *
     * Will only apply special price for multitudes of threshold quantity.
     * For the remainder regular price will be charged.
     *
     * @param \Epsi\PragmaticCart\Checkout\Quote $quote to work on
     * @param \Epsi\PragmaticCart\Store\Product $subject being under price review
     * @return int
     */
    public function getLineItemTotalForProduct(Quote $quote, Product $subject) {
        $quantityInTotal = $quote->getQuantity($subject);
        $threshold = $subject->getUnitsInBulk();
        $quantityInPromo = floor($quantityInTotal / $threshold);
        $quantityInFull = $quantityInTotal - $quantityInPromo;
        return ceil($quantityInPromo * $subject->getPriceInBulk() + $quantityInFull * $subject->getPrice());
    }

}