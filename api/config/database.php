<?php
//get mysql connection
class Database{
    //specify credentials
    private $host     = "localhost";
    private $db       = "api_db";
    private $username = "root";
    private $password = "";
    public  $charset  = "utf8mb4";
    public  $conn;

    //get database connection
    public function getConnection(){
        $dsn = "mysql:host=$this->host;dbname=$this->db;charset=$this->charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $this->conn = null;
        try{
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
        }catch (PDOException $e){
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }

        return $this->conn;
    }
}
