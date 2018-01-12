<?php

namespace Epsi\PragmaticCart\Checkout;

final class Quote {

    private $cart;
    private $productPromos = [];
    private $purchasePromos = [];
    private $lineItemTotals = [];
    private $grandTotal;

    public function __construct(Cart $cart) {
        $this->cart = $cart;
    }

    public function getQuantity(Product $product) {
        return $this->cart->getQuantity();
    }

    public function getPromos(Product $product) {
        $productId = $product->getId();
        if (isset($this->productPromos[$productId])) {
            return $this->productPromos[$productId];
        }
        return [];
    }

    public function calculate() {
        $catalog = Catalog::getInstance();
        $promos = Promo::getRegisteredPromos();

        // apply line item promos
        $this->lineItemTotals = [];
        foreach ($cart as $productId) {
            $product = $catalog->getProductById($productId);
            $this->lineItemTotals[$productId] = $product->getPrice() * $cart->getQuantity($product);
            foreach ($promos as $promo) {
                if ($promo->isEligibleOnProduct($this, $product)) {
                    $this->productPromos[$productId] = $promo;
                    $this->lineItemTotals[$productId] = $promo->getLineItemTotalForProduct($this, $product);
                }
            }
        }

        // apply entire purchase promos
        foreach ($promos as $promo) {
            if ($promo->isEligibleOnPurchase($this)) {
                $this->purchasePromos[] = $promo;
            }
        }

        $receipt = new Receipt($cart);
        foreach ($cart as $productId) {
            $product = $catalog->getProductById($productId);
            $quantity = $cart->getQuantity($product);
            $receipt->addLineItem($product, $quantity,
        }
        $receipt->addLineItem
}

}