<?php
//create database configurarion
$host= "localhost";
$dbname= "library";
$username= "root";
$password ="";
$port = 3305;

// create connection
$conn= new mysqli($host, $username, $password,$dbname, $port);
// check connection
if($conn-> connect_error){
    die("connection failed: " .$conn-> connect_error);
}