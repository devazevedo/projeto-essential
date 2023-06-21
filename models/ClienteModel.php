<?php

require_once('./configuration/connect.php');

class ClienteModel extends Connect
{
    private $table;

    function __construct()
    {
        parent::__construct();
        $this->table = 'clientes';
    }

    function getCustomers()
    {
        $sqlSelect = "SELECT * FROM $this->table";
        $statement = $this->connection->prepare($sqlSelect);
        $statement->execute();
        $resultQuery = $statement->fetchAll();
        return $resultQuery;
    }

    public function deleteCustomer($id)
    {
        $sqlDelete = "DELETE FROM $this->table WHERE id = :id";
        $statement = $this->connection->prepare($sqlDelete);
        $statement->bindParam(':id', $id);
        $statement->execute();

        return $statement->rowCount(); // Retorna o número de linhas afetadas pela exclusão
    }
}
