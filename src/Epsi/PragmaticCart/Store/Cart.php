<?php

namespace Epsi\PragmaticCart\Store;

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
final class Cart implements IteratorAggregate {

    const K_PRODUCT = 0;
    const K_QUANTITY = 1;

    /**
     * Line items indexed by product id
     * @var \Epsi\PragmaticCart\Checkout\LineItem<int>[]
     */
    private $lineItems = [];

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
            $this->lineItems[$productId] = new LineItem($product, $quantity);
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

}