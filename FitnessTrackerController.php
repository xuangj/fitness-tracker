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
                $this->showLogin();
                break;

            case "editProfile":
                $this->showEditProfile();
                break;
            //case "dashboard":
            case "welcome":
            default:
                $this->createAccount();
                $this->showWelcome(); // this is being hit for some reason and not case create account
                break;
        }

    }

    public function showWelcome($message=""){
        include(__DIR__ . '/login.php'); 
    }

    
    // Create Account logic
    public function showCreateAccount($message=""){
        include(__DIR__ . '/createAccount.php'); 
    }
    private function checkUserExist(){
        $query = "SELECT * FROM users WHERE email = $1;";
        $result = pg_query_params($this->db, $query, [$_POST["Email"]]);
        return pg_num_rows($result) > 0;
    }

    private function checkUsernameTaken(){
        $query = "SELECT * FROM users WHERE username = $1;";
        $result = pg_query_params($this->db, $query, [$_POST["Username"]]);
        return pg_num_rows($result) > 0;
    }

    public function createAccount($message =""){
        if (!isset($_POST["Name"]) || !isset($_POST["Email"]) 
            || !isset($_POST["Password"]) ||  !isset($_POST["Username"])
            || empty($_POST["Name"]) || empty($_POST["Username"])
            || empty($_POST["Email"]) || empty($_POST["Password"])){
                $this->showCreateAccount("Please fill in all information.");
                return;
        }

        if($this->checkUserExist() === true){
            $this->showCreateAccount("This email is linked to an existing account.  Would you like to log in?");
            return;
        }

        if($this->checkUsernameTaken() === true){
            $this->showCreateAccount("This username is already taken. Try something else!");
            return;
        }

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

    // Login logic
    public function showLogin($message=""){
        include(__DIR__ . '/login.php'); 
    }

    public function login($message=""){
        if (!isset($_POST["Email"]) || !isset($_POST["Password"]) || 
            empty($_POST["Password"]) || empty($_POST["Email"])) {
            $this->showWelcome("Missing information");
            return;
        }
        echo "hit";
        $email = trim($_POST["Email"]);
        $password = $_POST["Password"];

        $user = $this->retrieveUser($email);

        if (empty($user)){
            $this->showWelcome("This email is not connected to an account. Would you like to sign up?");
        
        } 
        if(password_verify($password, $user["password"])){
            $_SESSION["name"] = $user["name"];
            $_SESSION["email"] = $user["email"];
            $_SESSION["username"] = $user["username"];
        }
        else {
            $this-> showWelcome("<p class='alert alert-danger'>Incorrect password!</p>");
        }
        echo "success!";
        header("Location: ?command=createAccount");
        exit;
    }

    public function showEditProfile($message = ""){
        include(__DIR__ . '/edit-profile.php');
    }
    
}
