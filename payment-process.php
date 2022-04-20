<?php
require 'square/square-sdk/autoload.php';

use Square\SquareClient;
use Square\LocationApi;
use Square\Environment;
use Square\Exceptions\ApiException;
use Square\Http\ApiResponse;
use Square\Models\CreatePaymentRequest;
use Square\Models\ListLocationsResponse;
use Square\Models\Money;


$data = json_decode(file_get_contents('php://input'), true);

$square_client = new SquareClient([
    'accessToken' => "EAAAEFXbLVgWKvm0myOBdu9jMWcSsbMw3BgXSOuanCLsvVnlk53TaLVOHQq7lcnP",
    'environment' => Environment::SANDBOX,
]);

$payments_api = $square_client->getPaymentsApi();

$money = new Money();
$money->setAmount(intval($data['amount']));
$money->setCurrency("EUR");

// Every payment you process with the SDK must have a unique idempotency key.
// If you're unsure whether a particular payment succeeded, you can reattempt
// it with the same idempotency key without worrying about double charging
// the buyer.
$order_id = rand(1000, 9999);
$create_payment_request = new CreatePaymentRequest($data['sourceId'], $order_id, $money);

$response = $payments_api->createPayment($create_payment_request);

if ($response->isSuccess()) {
    var_dump($response->getResult());
  echo json_encode($response->getResult());
} else {
  echo json_encode($response->getErrors());
}
