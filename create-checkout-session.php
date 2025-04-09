<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require 'vendor/autoload.php'; // Stripe via Composer

\Stripe\Stripe::setApiKey('sk_test_51MqgSbKffElW1aFCCOZMrFMCleG3DdcOAangeYc8r9WQQPQ7gJeUwAzqBY2nUv2zMblk7luNGlPC8mCQyKzKGPWm00oEjcFij5'); // ta clé secrète test

header('Content-Type: application/json');

$input = json_decode(file_get_contents("php://input"), true);

$cart = $input['cart'];
$client = $input['client'];

$line_items = [];

foreach ($cart as $item) {
    $line_items[] = [
        'price_data' => [
            'currency' => 'eur',
            'unit_amount' => 5000, // 50.00€
            'product_data' => [
                'name' => 'Console rétro - ' . htmlspecialchars($item['color']),
            ],
        ],
        'quantity' => (int)$item['quantity'],
    ];
}
// ✅ Résumé de la commande
$commande_resume = implode(', ', array_map(function($item) {
    return $item['quantity'] . " x " . $item['color'];
}, $cart));
try {
    $session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => $line_items,
        'mode' => 'payment',
        'customer_email' => $client['email'],
		'metadata' => [
        'prenom' => $client['prenom'],
        'nom' => $client['nom'],
        'adresse' => $client['adresse'],
        'code_postal' => $client['code_postal'],
        'ville' => $client['ville'],
        'telephone' => $client['telephone'],
        'email' => $client['email'],
        'commande' => $commande_resume
    ],
        'success_url' => 'https://consolegbretro.onrender.com/success.php?session_id={CHECKOUT_SESSION_ID}', 
        'cancel_url' => 'https://consolegbretro.onrender.com/annule.html',
    ]);

    echo json_encode(['sessionId' => $session->id]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
