<?php

namespace Epsi\PragmaticCart\Checkout;

final class LineItem implements Promoable {

    private $product;
    private $quantity;
    private $promos = [];
    private $discount = 0;
    private $calculated = false;

    public function __construct(Product $product, $quantity) {
        $this->product = $product;
        $this->quantity = $quantity;
    }

    public function calculateDiscount() {
        $this->promos = [];
        $this->discount = 0;
        foreach (Promo::getRegisteredPromos() as $promo) {
            $discount = $promo->getLineItemDiscount()
            if ($discount > 0) {
                $this->promos[] = $promo;
                $this->discount += $discount;
            }
        }
        $this->calculated = true;
    }

    public function getProduct() {
        return $this->product;
    }

    public function getQuantity() {
        return $this->quantity;
    }

    public function getPromos() {
        $this->calculated or $this->calculateDiscount();
        return $this->promos;
    }

    public function getDiscountAmount() {
        $this->calculated or $this->calculateDisount();
        return $this->discount;
    }

    public function getTotalAmount() {
        return $this->product->getPrice() * $this->quantity - $this->getDiscount();
    }

}