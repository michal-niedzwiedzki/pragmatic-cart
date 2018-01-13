<?php

namespace \Epsi\PragmaticCart\Checkout;

interface Promoable {

    public function getAvailablePromos();

    public function getApplicablePromos();

    public function getDiscountAmount();

    public function getTotalAmount();

}