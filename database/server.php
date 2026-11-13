<?php
// Local XAMPP settings; replace these with the hosting database values before deployment.
$host = "localhost";
$dbUser = "root";
$dbPass = "password";

function connectDb($dbName) {
    global $host, $dbUser, $dbPass;
    try {
        $pdo = new PDO(
            "mysql:host=$host;dbname=$dbName;charset=utf8mb4",
            $dbUser,
            $dbPass,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        exit("Database connection failed."); //changed echo to exit -Joshua
    }
}

$sharedDb = connectDb("mentorshipmatch");
$mentorDb = $sharedDb;
$studentDb = $sharedDb;