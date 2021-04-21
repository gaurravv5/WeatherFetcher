<?php

$host = "localhost";

$root = "root";
$root_password = "nagarro";

$user = 'root';
$pass = 'newpass';
$db = "weatherdb";
$table = "weather";

try {
    $dbh = new PDO("mysql:host=$host", $root, $root_password);

    $dbh->exec("CREATE DATABASE IF NOT EXISTS `$db`;
            CREATE USER IF NOT EXISTS '$user'@'localhost' IDENTIFIED BY '$pass';
            GRANT ALL ON `$db`.* TO '$user'@'localhost';
            FLUSH PRIVILEGES;")
    or die(print_r($dbh->errorInfo(), true));

    $dbh->query("use $db");

    $sql = "CREATE TABLE IF NOT EXISTS $table (
    	id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    	city VARCHAR(255),
    	temperature DECIMAL(5,2),
    	created_at TIMESTAMP
	)";

	$dbh->exec($sql);
}
catch (PDOException $e) {
    die("DB ERROR: " . $e->getMessage());
}

