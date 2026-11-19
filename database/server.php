<?php

class DBConn
{
    private $host = 'localhost';
    private $user = 'root';
    private $pass = 'password';
    private $dbname = 'mentorshipmatch'; 
    private $conn;

    public function __construct()
    {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        try {
            $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->dbname);
            $this->conn->set_charset('utf8mb4');
        } catch (mysqli_sql_exception $e) {
            die('Connection Failed: ' . $e->getMessage());
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


$dbConn = new DBConn();
$db = $dbConn->get_connection();
?>