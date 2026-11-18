<?php

class DBConn
{
    private $host = 'localhost'; //IP Adress and Port for Database Server
    private $user = 'root'; //Username. Cannto be root for hosted applications because root gives admin acess
    private $pass = 'password'; //password
    private $dbname = 'tasklist'; // name of the scehma the app  will be using
    private $conn;

    public function __construct()
    {
        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->dbname);

        if ($this->conn->connect_error) {
            die('Connection Failed: ' . $this->conn->connect_error);
        }
    }

    public function get_connection()
    {
        return $this->conn;
    }

    public function close_connection()
    {
        $this->conn->close();
    }
}
?>
