<?php

    require_once('./controllers/ClienteController.php');

    $controller = new ClienteController();
    $action = !empty($_GET['action']) ? $_GET['action'] : (!empty($_POST['action']) ? $_POST['action'] : 'getCustomers');

    $controller->{$action}();

?>