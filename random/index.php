<?php
require(__DIR__ . '/vendor/autoload.php');
require_once(__DIR__ . '/vendor/stripe/stripe-php/init.php');

\Stripe\Stripe::setApiKey('sk_test_51HNn90Gpt3HnSDNnv6lWr8yhd0OqckVDbhaBY84FRZsF52hicx3uHozuG8WvLOw4tXrMyKLwbSv1C3sS1pkXFOOr00xa1Z0t8N');

use Slim\Http\Request;
use Slim\Http\Response;

$app->post('/stripe-webhook', function (Request $request, Response $response) {
    $logger = $this->get('logger');
    $event = $request->getParsedBody();
    $stripe = $this->stripe;

    // Parse the message body (and check the signature if possible)
    $webhookSecret = getenv('STRIPE_WEBHOOK_SECRET');
    if ($webhookSecret) {
        try {
            $event = $stripe->webhooks->constructEvent(
                $request->getBody(),
                $request->getHeaderLine('stripe-signature'),
                $webhookSecret
            );
        } catch (Exception $e) {
            return $response->withJson(['error' => $e->getMessage()])->withStatus(403);
        }
    } else {
        $event = $request->getParsedBody();
    }
    $type = $event['type'];
    $object = $event['data']['object'];

    // Handle the event
    // Review important events for Billing webhooks
    // https://stripe.com/docs/billing/webhooks
    // Remove comment to see the various objects sent for this sample
    switch ($type) {
        case 'invoice.paid':
            // The status of the invoice will show up as paid. Store the status in your
            // database to reference when a user accesses your service to avoid hitting rate
            // limits.
            $logger->info('🔔  Webhook received! ' . $object);
            break;
        case 'invoice.payment_failed':
            // If the payment fails or the customer does not have a valid payment method,
            // an invoice.payment_failed event is sent, the subscription becomes past_due.
            // Use this webhook to notify your user that their payment has
            // failed and to retrieve new card details.
            $logger->info('🔔  Webhook received! ' . $object);
            break;
        case 'invoice.finalized':
            // If you want to manually send out invoices to your customers
            // or store them locally to reference to avoid hitting Stripe rate limits.
            $logger->info('🔔  Webhook received! ' . $object);
            break;
        case 'customer.subscription.deleted':
            // handle subscription cancelled automatically based
            // upon your subscription settings. Or if the user
            // cancels it.
            $logger->info('🔔  Webhook received! ' . $object);
            break;
        case 'customer.subscription.trial_will_end':
            // Send notification to your user that the trial will end
            $logger->info('🔔  Webhook received! ' . $object);
            break;
            // ... handle other event types
        default:
            // Unhandled event type
    }

    return $response->withJson(['status' => 'success'])->withStatus(200);
});
