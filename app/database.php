<?php

class Database
{
    private $HOST = '[HOST]';
    private $USER = '[USER]';
    private $PASSWORD = '';
    private $DATABASE_NAME = '[DATABASE NAME]';

    public function connect()
    {
        $connection_query = "mysql:host=$this->HOST;dbname=$this->DATABASE_NAME";
        $connection = new PDO($connection_query, $this->USER, $this->PASSWORD);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $connection;
    }
}
