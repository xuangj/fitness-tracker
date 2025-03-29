<?php

class FitnessTrackerController{
    private $db;
    public function __construct($input){
        $host = "db";
        $port = "5432";
        $db = "example";
        $user = "localuser";
        $password = "cs4640LocalUser!"; 

        session_start();
        $this->input = $input;
        $this->db = pg_connect("host=$host port=$port dbname=$db user=$user password=$password");

    }

    public function run(){
        $command = "welcome";
        if (isset($this->input["command"]) 
            && ($this->input["command"] == "login" || isset($_SESSION["name"])))
            $command = $this->input["command"];

        switch($command){
            case "createAccount":
                $this->createAccount();
                break;
            case "login":
                $this->login();
                break;
            //case "dashboard":
            case "welcome":
            default:
                $this->showWelcome();
                break;
        }

    }

    public function showWelcome($message=""){
        include(__DIR__ . '/createAccount.html'); 
    }

    public function showCreateAccount($message=""){
        include(__DIR__ . '/createAccount.html'); 
    }

    private function checkUserExist(){
        $query = "SELECT * FROM users WHERE email = $1;";
        $result = pg_query_params($this->db, $query, [$_POST["Email"]]);
        echo "hit1";
        return pg_num_rows($result) > 0;
        
    }

    public function createAccount(){

        if (!isset($_POST["Name"]) || !isset($_POST["Email"]) 
            || !isset($_POST["Password"]) ||  !isset($_POST["Username"])
            || empty($_POST["Name"]) || empty($_POST["Username"])
            || empty($_POST["Email"]) || empty($_POST["Password"])){
                $this->showCreateAccount("Please fill in all information.");
        }

        if($this->checkUserExist() === false){
            $hashedPasswd = password_hash($_POST["Password"], PASSWORD_DEFAULT);
            $query = "insert into users (name, username, email, password) values ($1, $2, $3, $4);";
            $params = [$_POST["Name"], $_POST["Username"], $_POST["Email"], $hashedPasswd];
            $createUser = pg_query_params($this->db, $query, $params);

            
            $_SESSION["name"] = $_POST["Name"];
            $_SESSION["username"] = $_POST["Username"];
            $_SESSION["email"] = $_POST["Email"];
            
            header("Location: ?command=welcome");
            return;
        }
    }
}