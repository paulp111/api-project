<?php
require 'vendor/autoload.php';

use RedBeanPHP\R;

R::setup('mysql:host=localhost;dbname=api', 'root', '');

if (R::testConnection()) {
    echo "Datenbankverbindung erfolgreich!";
} else {
    echo "Datenbankverbindung fehlgeschlagen.";
}
