<?php

require __DIR__ . '/vendor/autoload.php'; // ← charge les classes

use MongoDB\Client;
use MongoDB\Driver\ServerApi;

$uri = 'mongodb+srv://ecoride_admin:u25q9n4uaXNDDlC4@ecoride-cluster.wwvibuk.mongodb.net/?retryWrites=true&w=majority&appName=ecoride-cluster';

$apiVersion = new ServerApi(ServerApi::V1);
$client = new Client($uri, [], ['serverApi' => $apiVersion]);

try {
    $client->selectDatabase('admin')->command(['ping' => 1]);
    echo "✅ Connexion à MongoDB réussie !\n";

    // Insertion dans la base ecoride-2025 > collection connection_log
    $log = [
        'userId' => '9999',
        'username' => 'admin@ecoride.com',
        'ip' => '127.0.0.1',
        'success' => true,
        'timestamp' => new \MongoDB\BSON\UTCDateTime()
    ];

    $client->selectDatabase('ecoride-2025')
        ->selectCollection('connection_log')
        ->insertOne($log);

    echo "✅ Log inséré dans la collection 'connection_log'\n";
} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage();
}
