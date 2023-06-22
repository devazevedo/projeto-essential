<?php

require_once('./configuration/connect.php');

class ClienteModel extends Connect
{
    private $table;

    function __construct()
    {
        parent::__construct();
    }

    function getCustomers($filterSoNumero = null, $filter = null)
    {
        $sqlSelect = "SELECT * FROM clientes WHERE 1=1";

        if (!empty($filterSoNumero)) {
            $sqlSelect .= " AND (id = :id OR cpf = :cpf OR celular = :celular OR nome LIKE :nome OR email LIKE :email)";
        } else if (!empty($filter) && empty($filterSONumero)) {
            $sqlSelect .= " AND (nome LIKE :nome OR email LIKE :email)";
        }


        $nome = "%$filter%";
        $email = "%$filter%";
        $statement = $this->connection->prepare($sqlSelect);

        if (!empty($filterSoNumero)) {
            $statement->bindParam(':id', $filterSoNumero);
            $statement->bindParam(':cpf', $filterSoNumero);
            $statement->bindParam(':celular', $filterSoNumero);
        }

        if (!empty($filter)) {
            $statement->bindParam(':nome', $nome);
            $statement->bindParam(':email', $email);
        }

        $statement->execute();
        $resultQuery = $statement->fetchAll();
        return $resultQuery;
    }


    function checkExistingCustomer($cpf, $email, $celular)
    {
        $conflictingFields = array();

        $sqlSelect = "SELECT cpf, email, celular FROM clientes WHERE cpf = :cpf OR email = :email OR celular = :celular";
        $statement = $this->connection->prepare($sqlSelect);
        $statement->bindParam(':cpf', $cpf);
        $statement->bindParam(':email', $email);
        $statement->bindParam(':celular', $celular);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            if ($result['cpf'] == $cpf) {
                $conflictingFields[] = 'cpf';
            }
            if ($result['email'] == $email) {
                $conflictingFields[] = 'email';
            }
            if ($result['celular'] == $celular) {
                $conflictingFields[] = 'celular';
            }
        }

        return $conflictingFields;
    }


    public function deleteCustomer($id)
    {
        $sqlDelete = "DELETE FROM clientes WHERE id = :id";
        $statement = $this->connection->prepare($sqlDelete);
        $statement->bindParam(':id', $id);
        $statement->execute();

        return $statement->rowCount();
    }

    function insertCustomer($nome, $cpf, $email, $celular, $fotoPath)
    {
        $sqlInsert = "INSERT INTO clientes (nome, cpf, email, celular, foto) VALUES (:nome, :cpf, :email, :celular, :foto)";
        $statement = $this->connection->prepare($sqlInsert);
        $statement->bindParam(':nome', $nome);
        $statement->bindParam(':cpf', $cpf);
        $statement->bindParam(':email', $email);
        $statement->bindParam(':celular', $celular);
        $statement->bindParam(':foto', $fotoPath);

        $resultado = $statement->execute();

        return $resultado;
    }

    function updateCustomerWithPhoto($id, $nome, $cpf, $email, $celular, $fotoPath)
    {
        $sqlUpdate = "UPDATE clientes SET nome = :nome, cpf = :cpf, email = :email, celular = :celular, foto = :foto WHERE id = :id";
        $statement = $this->connection->prepare($sqlUpdate);
        $statement->bindParam(':id', $id);
        $statement->bindParam(':nome', $nome);
        $statement->bindParam(':cpf', $cpf);
        $statement->bindParam(':email', $email);
        $statement->bindParam(':celular', $celular);
        $statement->bindParam(':foto', $fotoPath);

        return $statement->execute();
    }

    function updateCustomer($id, $nome, $cpf, $email, $celular)
    {
        $sqlUpdate = "UPDATE clientes SET nome = :nome, cpf = :cpf, email = :email, celular = :celular WHERE id = :id";
        $statement = $this->connection->prepare($sqlUpdate);
        $statement->bindParam(':id', $id);
        $statement->bindParam(':nome', $nome);
        $statement->bindParam(':cpf', $cpf);
        $statement->bindParam(':email', $email);
        $statement->bindParam(':celular', $celular);

        return $statement->execute();
    }
}
