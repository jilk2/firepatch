<?php

$db = mysqli_connect(
    "localhost",
    "root",
    "",
    "tle-1"
);

if (!$db) {
    die("Database verbinding mislukt: " . mysqli_connect_error());
}