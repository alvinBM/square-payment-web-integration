<?php
require './vendor/autoload.php';
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

$users = getUser();
$notifications = [];






$webPush = new WebPush([
    'VAPID' => [
        'subject' => 'alvinbauma10@gmail.com',
        'publicKey' => 'BAPZ1BFHrRDyLViCFQb4wYiG2vsboP-nvkbz8qZh_MKLM7vI7AMz3ZEAJ32i6gRwwCzmuSIDUsfl4d5ke1aQ8DI',
        'privateKey' => 'Nli3I2SvLz7v_pkrXUZ10_quHMNkskMa8T5Wcqwtp-8',
    ],
]);


foreach($users as $subscription) {
    $webPush->queueNotification(
        Subscription::create([
            'endpoint' => $subscription->endpoint,
            'publicKey' => $subscription->public_key,
            'authToken' => $subscription->auth,
        ]),
        json_encode([
            'message' => 'Bonjour les gens',
            'title' => 'Vous avez un nouveau message'
        ])
    );
}
foreach ($webPush->flush() as $report) {
    $endpoint = $report->getRequest()->getUri()->__toString();
    if ($report->isSuccess()) {
        var_dump("[v] Le message bien été envoyé {$endpoint}.");
    } else {
        var_dump("[x] Impossible d'envoyer le message {$endpoint}: {$report->getReason()}");
    }
}








// // var_dump($users); die;

// foreach($users as $subscription) {
//     $notifications []= [
//         'subscription' => Subscription::create([
//             'endpoint' => $subscription->endpoint,
//             'publicKey' => $subscription->public_key,
//             'authToken' => $subscription->auth
//         ]),
//         'payload' => 'Vous avez un nouveau message !',
//     ];
// }

// $webPush = new WebPush();


// // send multiple notifications with payload
// foreach ($notifications as $notification) {
//     echo "Notifications data<hr/>";
//     var_dump($notification);
//     echo "<hr/>";
//     $webPush->queueNotification(
//         $notification['subscription'],
//         $notification['payload'] // optional (defaults null)
//     );
// }

// foreach ($webPush->flush() as $report) {
//     $endpoint = $report->getRequest()->getUri()->__toString();
//     if ($report->isSuccess()) {
//         var_dump("[v] Le message bien été envoyé {$endpoint}.");
//     } else {
//         var_dump("[x] Impossible d'envoyer le message {$endpoint}: {$report->getReason()}");
//     }
// }



function getUser(){
    try {
        $dbh = new PDO('mysql:host=localhost;dbname=test_push', "root", "root");
        $users = $dbh->query('SELECT * from users');
        return $users->fetchAll(PDO::FETCH_OBJ);
    } catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage() . "<br/>";
        return null;
        die();
    }
}