<?php
require 'vendor/autoload.php';
\Stripe\Stripe::setApiKey('sk_test_51MqgSbKffElW1aFCCOZMrFMCleG3DdcOAangeYc8r9WQQPQ7gJeUwAzqBY2nUv2zMblk7luNGlPC8mCQyKzKGPWm00oEjcFij5'); // ta clé secrète test

// 1. Vérifie que l'ID de session est présent dans l'URL
if (!isset($_GET['session_id'])) {
    echo "Session Stripe introuvable.";
    exit;
}

$session_id = $_GET['session_id'];

try {
    // 2. Récupère les infos de session depuis Stripe
    $session = \Stripe\Checkout\Session::retrieve($session_id);
    $customer_email = $session->customer_email;
    $metadata = $session->metadata;

    // 3. Données client depuis metadata
    $prenom = htmlspecialchars($metadata->prenom ?? 'Client');
    $nom = htmlspecialchars($metadata->nom ?? '');
    $adresse = htmlspecialchars($metadata->adresse ?? '');
    $ville = htmlspecialchars($metadata->ville ?? '');
    $code_postal = htmlspecialchars($metadata->code_postal ?? '');
    $telephone = htmlspecialchars($metadata->telephone ?? '');
    $email = htmlspecialchars($metadata->email ?? $customer_email ?? '');

    // 4. Contenu du panier si stocké en metadata (facultatif)
    $commande_resume = htmlspecialchars($metadata->commande ?? 'Console rétro');

} catch (Exception $e) {
    echo "Erreur lors de la récupération de la commande : " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Confirmation de commande</title>
	<meta http-equiv="refresh" content="10;URL=index.html"> <!-- ✅ Redirection automatique -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f3f3f3;
            padding: 2rem;
            color: #333;
        }
        .box {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            max-width: 700px;
            margin: auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #28a745;
        }
        p {
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="box">
        <h1>🎉 Merci pour votre commande, <?= $prenom ?> !</h1>
        <p>Votre commande a bien été enregistrée et est en cours de traitement.</p>

        <h3>Détails de la commande :</h3>
        <p><strong>Produit :</strong> <?= $commande_resume ?></p>

        <h3>Coordonnées :</h3>
        <p><strong>Nom :</strong> <?= $prenom . ' ' . $nom ?></p>
        <p><strong>Adresse :</strong> <?= $adresse . ', ' . $code_postal . ' ' . $ville ?></p>
        <p><strong>Téléphone :</strong> <?= $telephone ?></p>
        <p><strong>Email :</strong> <?= $email ?></p>

        <p>📩 Un email de confirmation va vous être envoyé à <strong><?= $email ?></strong>.</p>

        <p>Merci pour votre confiance !<br>L'équipe ConsoleGBRetro.</p>
		
		     <div class="retour">
            <p>⏳ Vous allez être redirigé vers la boutique dans quelques secondes...</p>
            <p><a href="index.html">Cliquez ici</a> si la redirection ne fonctionne pas.</p>
        </div>
		
    </div>
</body>
</html>
