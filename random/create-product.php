<?php
require(__DIR__ . '/vendor/autoload.php');
require_once(__DIR__ . '/vendor/stripe/stripe-php/init.php');

\Stripe\Stripe::setApiKey('sk_test_51HNn90Gpt3HnSDNnv6lWr8yhd0OqckVDbhaBY84FRZsF52hicx3uHozuG8WvLOw4tXrMyKLwbSv1C3sS1pkXFOOr00xa1Z0t8N');


$product = \Stripe\Product::create([
    'name' => 'Starter Dashboard',
]);

