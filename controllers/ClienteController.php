<?php

require_once('./models/ClienteModel.php');

class ClienteController
{
    private $model;

    public function __construct()
    {
        $this->model = new ClienteModel();
    }

    public function getCustomers()
    {
        $clientes = $this->model->getCustomers();
        echo json_encode($clientes);
    }

    public function deleteCustomer()
    {
        if (isset($_POST['id'])) {
            $id = $_POST['id'];
            $exclusao = $this->model->deleteCustomer($id);
            echo json_encode($exclusao);
        } else {
            echo json_encode(false);
        }
    }
}
?>
