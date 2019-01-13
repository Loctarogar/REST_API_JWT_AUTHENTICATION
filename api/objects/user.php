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

    public function update(){
        //if password needs to be updated
        $password_set =! empty($this->password) ? ", password = :password " : " ";
        //if no posted password, do not update the password
        $query = "UPDATE ".$this->table_name."
                  SET
                      firstname = :firstname,
                      lastname  = :lastname,
                      email     = :email
                      {$password_set}
                  WHERE id = :id";

        //prepare the query
        $stmt = $this->conn->prepare($query);

        //
        //here i need to sanitize
        //

        //bind the values from the form
        $stmt->bindParam('firstname', $this->firstname);
        $stmt->bindParam('lastname' , $this->lastname);
        $stmt->bindParam('email', $this->email);

        //hash the password before adding to the database
        if(!empty($this->password)){
            $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
            $stmt->bindParam('password', $this->password);
        }
        $stmt->bindParam('id', $this->id);
        if($stmt->execute()){
            return true;
        }
        /**
        $execute_stmt = $stmt->execute([
                        'firstname'=> $this->firstname,
                        'lastname' => $this->lastname,
                        'email'    => $this->email,
                        'password' => $password_hash,
                        'id'       => $this->id
                       ]);
        if($execute_stmt){
            return true;
        }
         */

        return false;
    }
}

















