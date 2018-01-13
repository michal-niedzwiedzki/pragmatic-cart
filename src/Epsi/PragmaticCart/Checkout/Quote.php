<?php

namespace Epsi\PragmaticCart\Checkout;

final class Quote {

    private $cart;

    /**
     * List of available promotions
     * @var \Epsi\PragmaticCart\Promo\Promo[]
     */
    private $availablePromos = [];

    /**
     * List of applicable promotions
     * @var \Epsi\PragmaticCart\Promo\Promo[]
     */
    private $applicablePromos = [];

    private $purchaseTotal = 0;
    private $discount = 0;
    private $grandTotal = 0;

    private $calculated = false;

    public function __construct(Cart $cart) {
        $this->cart = $cart;
    }

    public function getProductQuantity(Product $product) {
        return $this->cart->getQuantity($product);
    }

    public function getProductPromos(Product $product) {
        return $this->cart->getPromos($product);
    }

    public function getAvailablePromos() {
        return $this->promos;
    }

    public function calculateDiscount() {
        // obtain purchase total reduced by applicable line item promos
        $this->purchaseTotal = $this->cart->getTotal();;
        $this->grandTotal = $this->purchaseTotal;

        // apply purchase promos
        $this->applicablePromos = [];
        $this->discount = 0;
        foreach ($this->availablePromos as $promo) {
            $discount = $promo->getCartDiscount($this);
            if ($discount > 0) {
                $this->applicablePromos[] = $promo;
                $this->grandTotal = ($discount < $this->grandTotal) ? ($this->grandTotal - $discount) : 0;
            }
        }
        $this->calculated = true;
    }

}