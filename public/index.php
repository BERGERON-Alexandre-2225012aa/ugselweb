<?php

require "../app/controllers/DisplayController.php";
use app\controllers\DisplayController;

$path = ($_SERVER['REQUEST_URI']);

$controller = new DisplayController($path);
$controller->display();

?>