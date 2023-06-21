<?php

    define('HOST','localhost');
    define('DATABASENAME','essential');
    define('USER', 'adminTeste');
    define('PASSWORD','Admin12#');

    class Connect{
        protected $connection;

        function __construct()
        {
            $this->connectDatabase();
        }

        function connectDatabase () 
        {
            try 
            {
                $this->connection = new PDO('mysql:host='.HOST.';dbname='.DATABASENAME, USER, PASSWORD);
            }
            catch (PDOException $e)
            {
                echo "Error!".$e->getMessage();
                die();
            }
        }
    }

?>