<?php

namespace Epsi\PragmaticCart\Checkout;

use \Epsi\PragmaticCart\Store\Product;

/**
 * Shopping cart
 *
 * Stores information about line items in cart (products and quantities).
 * Can add/remove products to/from cart.
 * Stores information about available promotions.
 * Can generate a quote based on cart contents and applicable promotions.
 *
 * @author Michał Rudnicki <michal@epsi.pl>
 */
final class Cart implements Quote {

    /**
     * Line items indexed by product id
     * @var \Epsi\PragmaticCart\Checkout\LineItem<int>[]
     */
    private $lineItems = [];

    /**
     * Calculated purchase amount
     * @var int
     */
    private $amount = 0;

    /**
     * Calculated discount amount
     * @var int
     */
    private $discount = 0;

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

    /**
     * Flag if discount calculation already performed
     * @var int
     */
    private $calculated = false;

    /**
     * Constructor
     *
     * @param \Epsi\PragmaticCart\Promo\Promo[] $promos
     */
    public function __construct(array $promos = []) {
        $this->availablePromos = $promos;
    }

    /**
     * Returns line items in cart
     *
     * @return \Epsi\PragmaticCart\Checkout\LineItem<int>[]
     */
    public function getLineItems() {
        return $this->lineItems;
    }

    /**
     * Add product to cart in given quantity and return self
     *
     * @param \Epsi\PagmaticCart\Store\Product
     * @param int $quantity
     * @return Epsi\PragmaticCart\Store\Cart
     */
    public function add(Product $product, $quantity) {
        $productId = $product->getId();
        if (isset($this->lineItems[$productId])) {
            $this->lineItems[$productId]->modifyQuantityBy($quantity);
        } else {
            $this->lineItems[$productId] = new LineItem($product, $quantity, $this->availablePromos);
        }
        return $this;
    }

    /**
     * Remove given quantity of product from cart and return self
     *
     * @param \Epsi\PagmaticCart\Store\Product
     * @param int $quantity
     * @return Epsi\PragmaticCart\Store\Cart
     */
    public function remove(Product $product, $quantity) {
        $productId = $product->getId();
        if (isset($this->lineItems[$productId]) and $this->lineItems[$productId]->getQuantity() > $quantity) {
            $this->lineItems[$productId]->modifyQuantityBy(-$quantity);
        } else {
            unset($this->lineItems[$productId]);
        }
        return $this;
    }

    public function getAmount() {
        $this->calculated or $this->calculate();
        return $this->amount;
    }

    public function getDiscount() {
        $this->calculated or $this->calculate();

    }

    public function getTotal() {
        $this->calculated or $this->calculate();
        return $this->amount - $this->discount;
    }

    public function getAvailablePromos() {
        return $this->availablePromos;
    }

    public function getApplicablePromos() {
        $this->calculated or $this->calculateDiscount();
        return $this->applicablePromos;
    }

    protected function calculate() {
        // collect line item amounts to calculate amount
        $this->amount = 0;
        foreach ($this->lineItems as $lineItem) {
            $this->amount += $lineItem->getTotalAmount();
        }
        $this->grandTotal = $this->purchaseTotal;

        // apply purchase promos to calculate discount
        $this->applicablePromos = [];
        $this->discount = 0;
        foreach ($this->availablePromos as $promo) {
            $discount = $promo->getCartDiscount($this);
            if ($discount > 0) {
                $this->applicablePromos[] = $promo;
                $this->grandTotal = ($discount < $this->grandTotal) ? ($this->grandTotal - $discount) : 0;
            }
        }

        // set flag to prevent needless recalculation
        $this->calculated = true;
    }

    public function getReceipt() {
        $receipt = [];
        $i = 0;
        foreach ($this->lineItems as $lineItem) {
            $p = [];
            foreach ($lineItem->getApplicablePromos() as $promo) {
                $p[] = [
                    "description" => $promo->getDescription(),
                    "discount" => -$promo->getLineItemDiscount($lineItem),
                ];
            }
            $receipt[] = [
                "item" => ++$i,
                "name" => $lineItem->getProduct()->getName(),
                "price" => $lineItem->getProduct()->getPrice(),
                "qty" => $lineItem->getQuantity(),
                "amount" => $lineItem->getAmount(),
                "discount" => -$lineItem->getDiscount(),
                "effective" => $lineItem->getTotal(),
                "promos" => $p,
            ];
        }
        return $receipt;
    }
}