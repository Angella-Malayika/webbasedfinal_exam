<?php
//create database configurarion
$host= "localhost";
$dbname= "library";
$username= "root";
$password ="1234";

// create connection
$conn= new mysqli($host, $username, $password,$dbname);
// check connection
if($conn-> connect_error){
    die("connection failed: " .
    $conn-> connect_error);
}