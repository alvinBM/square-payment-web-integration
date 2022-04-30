<?php
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
