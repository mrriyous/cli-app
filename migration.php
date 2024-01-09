<?php

use App\Services\PDOWrapper;

require 'vendor/autoload.php';

// Adjust these credentials according to your database setup
$host = 'localhost';
$dbname = 'db_user_cli';
$username = 'root';
$password = '';

$pdoWrapper = new PDOWrapper($host, $dbname, $username, $password);

// Define your database tables and schema here
$createUsersTable = "
    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
";

$createDoctorsTable = "
    CREATE TABLE IF NOT EXISTS doctors (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        specialty VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
";

$createCheckupSchedulesTable = "
    CREATE TABLE IF NOT EXISTS checkup_schedules (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        doctor_id INT NOT NULL,
        date DATE NOT NULL,
        start_time TIME NOT NULL,
        end_time TIME NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (doctor_id) REFERENCES doctors(id)  ON DELETE CASCADE
    )
";

// Execute the migration queries
// $pdoWrapper->executeStatement($createUsersTable);
// $pdoWrapper->executeStatement($createDoctorsTable);
$pdoWrapper->executeStatement($createCheckupSchedulesTable);

echo "Migration successful!\n";