<?php

require_once('./models/ClienteModel.php');

class ClienteController
{
    private $model;

    function __construct()
    {
        $this->model = new ClienteModel();
    }

    function getCustomers()
    {
        if(!empty($_GET['filter'])){
            $filter = $_GET['filter'];
            $filterSoNumero = null;
            if (!empty($filter)) {
                $filterSoNumero = preg_replace('/[^\d]/', '', $filter);
                if (!empty($filterSoNumero)) {
                    $filterSoNumero = intval($filterSoNumero);
                }
            }
            $clientes = $this->model->getCustomers($filterSoNumero, $filter);
        }else{
            $clientes = $this->model->getCustomers();
        }
        echo json_encode($clientes);
    }

    function deleteCustomer()
    {
        if (isset($_POST['id'])) {
            $id = $_POST['id'];
            $exclusao = $this->model->deleteCustomer($id);
            echo json_encode($exclusao);
        } else {
            echo json_encode(false);
        }
    }

    function addCustomer()
    {
        $nome = $_POST['nome'];
        $cpf = $_POST['cpf'];
        $email = $_POST['email'];
        $celular = $_POST['celular'];
        $id = $_POST['id'];
        if (!empty($_FILES)) {
            $foto = $_FILES['foto'];
        }

        if (empty($id)) {
            $conflictingFields = $this->model->checkExistingCustomer($cpf, $email, $celular);
            if (!empty($conflictingFields)) {
                $conflitos = 'Os dados informados já estão relacionados à outro cliente: ';
                foreach ($conflictingFields as $key => $value) {
                    if ($key != 0) {
                        $conflitos .= ', ';
                    }
                    $conflitos .= '<b>' . $value . '</b>';
                }
                $conflitos .= ' altere os dados informados e tente novamente.';
                echo json_encode(['status' => 0, 'mensagem' => $conflitos]);
                exit;
            } else {
                $destinationPath = 'assets';
                $fotoPath = $destinationPath . '/' . $foto['name'];
                move_uploaded_file($foto['tmp_name'], $fotoPath);

                $insercao = $this->model->insertCustomer($nome, $cpf, $email, $celular, $fotoPath);
                if ($insercao) {
                    echo json_encode(['status' => 1, 'mensagem' => 'Cliente cadastrado.']);
                } else {
                    echo json_encode(['status' => 0, 'mensagem' => 'Erro ao cadastrar o cliente.']);
                }
            }
        } else {
            if (!empty($foto)) {
                $destinationPath = 'assets';
                $fotoPath = $destinationPath . '/' . $foto['name'];
                move_uploaded_file($foto['tmp_name'], $fotoPath);
                $this->model->updateCustomerWithPhoto($id, $nome, $cpf, $email, $celular, $fotoPath);
            } else {
                $this->model->updateCustomer($id, $nome, $cpf, $email, $celular);
            }

            echo json_encode(['status' => 1, 'mensagem' => 'Cliente editado com sucesso.']);
        }
    }
}