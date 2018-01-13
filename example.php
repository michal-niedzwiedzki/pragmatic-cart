<?php

require_once "vendor/autoload.php";

use \Epsi\PragmaticCart\Store\Catalog;
use \Epsi\PragmaticCart\Store\Product;
use \Epsi\PragmaticCart\Checkout\Cart;
use \Epsi\PragmaticCart\Checkout\Quote;
use \Epsi\PragmaticCart\Promo\BulkDiscount;
use \Epsi\PragmaticCart\Promo\BundleDiscount;
use \Epsi\PragmaticCart\Promo\PercentDiscount;

// prepare catalog
$catalog = new Catalog();
$catalog->load("var/catalog.json");

// prepare available promotions
$promos = [
    new BulkDiscount("Bulk discount", $catalog->getProductById("A"), false),
    new BulkDiscount("Bulk discount", $catalog->getProductById("B"), false),
    new BulkDiscount("Bulk discount", $catalog->getProductById("C"), false),
    new BulkDiscount("Bulk discount", $catalog->getProductById("D"), false),
    new BundleDiscount("2 items bundle", [$catalog->getProductById("A"), $catalog->getProductById("B")], 10),
    new BundleDiscount("4 items bundle", [$catalog->getProductById("A"), $catalog->getProductById("B"), $catalog->getProductById("C"), $catalog->getProductById("D")], 30),
    new PercentDiscount("Heavy cart discount", 600, 10),
];
// TODO: move manual promo configuration to a config file

// create cart initiated with available promotions and throw in some products
$cart = new Cart($promos);
$cart->add($catalog->getProductById("A"), 3);
$cart->add($catalog->getProductById("A"), 1);
$cart->add($catalog->getProductById("B"), 3);
$cart->add($catalog->getProductById("C"), 200);

// get a recepit from and print it
$receipt = $cart->getReceipt();
print(json_encode($receipt, JSON_PRETTY_PRINT));
print("\n");