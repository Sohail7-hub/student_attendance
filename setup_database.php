<?php
$conn = new mysqli('localhost', 'root', '');
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// Drop and recreate database
$conn->query('DROP DATABASE IF EXISTS student_attendance');
$conn->query('CREATE DATABASE student_attendance');
$conn->select_db('student_attendance');

// Read and execute SQL files
$drop_sql = file_get_contents(__DIR__ . '/drop_tables.sql');
$create_sql = file_get_contents(__DIR__ . '/query.sql');

// Execute queries
if ($conn->multi_query($drop_sql . $create_sql)) {
    do {
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->next_result());
}

if ($conn->error) {
    die('Error executing SQL: ' . $conn->error);
}

echo 'Database setup completed successfully!';
$conn->close();
