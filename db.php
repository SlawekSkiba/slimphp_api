<?php
require __DIR__.'/config.php';

$mysqli = new mysqli(db_servername, db_username, db_password, db_name, 3306);

if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: " . $db->connect_errno;
    die("Failed to connect to MySQL" . $db->connect_errno);
}