<?php
require 'square/square-sdk/autoload.php';

use Square\SquareClient;
use Square\LocationApi;
use Square\Environment;
use Square\Exceptions\ApiException;
use Square\Http\ApiResponse;
use Square\Models\ListLocationsResponse;


$client = new SquareClient([
    'accessToken' => "EAAAEFXbLVgWKvm0myOBdu9jMWcSsbMw3BgXSOuanCLsvVnlk53TaLVOHQq7lcnP",
    'environment' => Environment::SANDBOX,
]);
