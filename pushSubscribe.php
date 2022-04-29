<?php
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;
$request = file_get_contents('php://input');
$request = json_decode($request);


// $_SESSION['subscribtions'] = [];

registerSubscribtion($request->endpoint, $request->keys->p256dh, $request->keys->auth);


function registerSubscribtion($endpoint, $public_key, $auth){

    $pdo = connectDB();
    
    $sql = "INSERT INTO users (endpoint, public_key, auth) VALUES (?,?,?)";
    $stmt= $pdo->prepare($sql);
    $stmt->execute([$endpoint, $public_key, $auth]);


    echo "Register user";
    foreach($pdo->query('SELECT * from users') as $row) {
        print_r($row);
    }
    
}





function connectDB(){
    try {
        $dbh = new PDO('mysql:host=localhost;dbname=test_push', "root", "root");
        foreach($dbh->query('SELECT * from users') as $row) {
            print_r($row);
        }
        return $dbh;
    } catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage() . "<br/>";
        return null;
        die();
    }
}


die;

// $webPush = new WebPush([
//     'VAPID' => [
//         'subject' => 'mailto:contact@grafikart.fr',
//         'publicKey' => 'BAPZ1BFHrRDyLViCFQb4wYiG2vsboP-nvkbz8qZh_MKLM7vI7AMz3ZEAJ32i6gRwwCzmuSIDUsfl4d5ke1aQ8DI',
//         'privateKey' => 'Nli3I2SvLz7v_pkrXUZ10_quHMNkskMa8T5Wcqwtp-8',
//     ],
// ]);

// foreach($user->subscriptions as $subscription) {
//     $webPush->queueNotification(
//         Subscription::create([
//             'endpoint' => $subscription->endpoint,
//             'publicKey' => $subscription->public_key,
//             'authToken' => $subscription->auth_token,
//         ]),
//         json_encode([
//             'message' => 'Bonjour les gens',
//             'title' => 'Mon titre'
//         ]);
//     );
// }

// foreach ($webPush->flush() as $report) {
//     $endpoint = $report->getRequest()->getUri()->__toString();
//     if ($report->isSuccess()) {
//         dump("[v] Le message bien été envoyé {$endpoint}.");
//     } else {
//         dump("[x] Impossible d'envoyer le message {$endpoint}: {$report->getReason()}");
//     }
// }