<?php

$ip = $_SERVER['REMOTE_ADDR'] ?? '-';
$pageTitle = $_GET['title'] ?? '';
$ua = $_SERVER['HTTP_USER_AGENT'] ?? '-';
$ref = $_SERVER['HTTP_REFERER'] ?? '-';

file_put_contents("./track.log", implode(PHP_EOL, [$pageTitle, $ip, $ua, $ref, time()]) . PHP_EOL, FILE_APPEND | LOCK_EX);

$dbConn = pg_connect("host=localhost port=5432 dbname=testdb user=admin password=password");

if (!$dbConn) {
	die('Could not connect: ' . pg_last_error());
}

$result = pg_query_params(
    $dbConn,
    "INSERT INTO visit_test (ip, page, ua, ref, time) VALUES ($1, $2, $3, $4, $5)",
    [$ip, $pageTitle, $ua, $ref, time()]
);

if ($result) {
	echo "Row inserted successfully.";
} else {
	echo "Insert failed: " . pg_last_error($dbConn);
}
