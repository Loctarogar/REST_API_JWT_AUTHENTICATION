<?php

class User{
    //database connection
    private $conn;
    private $table_name = "users";
    //object properties
    public $id;
    public $firstname;
    public $lastname;
    public $email;
    public $password;
    //construct
    public function __construct($db)
    {
        $this->conn = $db;
    }

    function create(){
        //insert query
        echo "create start";
        $query = "INSERT INTO 
                      ".$this->table_name."
                  VALUES (
                      0,
                      :firstname,
                      :lastname,
                      :email,
                      :passwordhash,
                      :created,
                      :updated)";
        $now = date ('Y-m-d H:i:s', time());
        echo "before query\n";
        $stmt = $this->conn->prepare($query);
        echo "after query \n";
        //hash password
        $passwordHash = password_hash($this->password, PASSWORD_BCRYPT);

        $result = $stmt->execute([
            'firstname' => $this->firstname,
            'lastname'  => $this->lastname,
            'email'     => $this->email,
            'passwordhash'  => $passwordHash,
            'created' => $now,
            'updated' => $now
        ]);
        echo "create finish\n";
        if($result){
            return true;
        }

        return false;
    }

    function emailExists(){
        //query to check if email exists
        $query = "SELECT id, firstname, lastname, password
                  FROM ".$this->table_name."
                  WHERE email = ?
                  LIMIT 0, 1";
        //prepare the query
        $stmt = $this->conn->prepare($query);

        $stmt->execute([$this->email]);

        // get number of rows
        $num = $stmt->rowCount();

        //if email exists, assign values to object properties
        if($num > 0){
            //get record details/values
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            //assign values to object properties
            $this->id = $row['id'];
            $this->firstname = $row['firstname'];
            $this->lastname = $row['lastname'];
            $this->password = $row['password'];

            //return true if email exists
            return true;
        }
        return false;
    }
}
