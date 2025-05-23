<?php
session_start();

//include 'config.php';


class FitnessTrackerController{
    private $db;
    public function __construct($input){
        // for server
        $host = "localhost";
        $port = 5432;
        $dbname = "pnq6th";
        $user = "pnq6th";
        $password = "sWYvrJqwKYgB";
        /* $host = "db";
        $port = "5432";
        $dbname = "example";
        $user = "localuser";
        $password = "cs4640LocalUser!"; */

        $this->input = $input;
        $this->db = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

    }

    public function run(){
        $command = "welcome";
        if (isset($this->input["command"]) && 
            ($this->input["command"] == "login" || 
            $this->input["command"] == "createAccount" ||
            isset($_SESSION["name"] ))) {

                 $command = $this->input["command"];
            }
           

        switch($command){
            case "createAccount":
                $this->createAccount();
                $this->showCreateAccount();
                break;
            case "login":
                $this->login();
                $this->showLogin();
                break;
            case "visitProfile":
                $this->visitProfile();
                break;
            case "editProfile":
                $this->showEditProfile();
                break;
            case "apiInfo":
                $this->sendUserInfoAPI();
                break;
            //case "dashboard":
            case "welcome":
            default:
                $this->showWelcome();
                break;
        }

    }

    public function showWelcome($message=""){
        include(__DIR__ . '/login.php'); 
    }

    // Create Account logic
    public function showCreateAccount($message=""){
        $_SESSION['create_account_message'] = $message;

        include(__DIR__ . '/createAccount.php'); 
    }
    
    // check if user exists by using their email
    private function checkUserExist($email){
        $query = "SELECT * FROM ftUsers WHERE email = $1;";

        $result = pg_query_params($this->db, $query, [$email]);
        if ($result === false) {
            error_log("Database error in checkUserExist: " . pg_last_error($this->db));
            return false;
        }
        return pg_num_rows($result) > 0;
    }

    // check if username is taken
    private function checkUsernameTaken($username){
        $query = "SELECT * FROM ftUsers WHERE username = $1;";
        $result = pg_query_params($this->db, $query, [$username]);
        if ($result === false) {
            error_log("Database error in checkUsernameTaken: " . pg_last_error($this->db));
            return false;
        }
        return pg_num_rows($result) > 0;
    }

    public function createAccount($message =""){
        $message = "";
        $passwd = trim($_POST["Password"]);

        if (isset($_POST["Name"]) && isset($_POST["Email"]) 
            && isset($_POST["Password"]) &&  isset($_POST["Username"])
            && isset($_POST["Gender"]) && isset($_POST["Age"])
            && isset($_POST["Feet"]) && isset($_POST["Inches"])
            && isset($_POST["Weight"]) && !empty($_POST["Name"]) 
            && !empty($_POST["Username"]) && !empty($_POST["Email"]) 
            && !empty($_POST["Password"]) && !empty($_POST["Gender"]) 
            && !empty($_POST["Age"]) && !empty($_POST["Feet"]) 
            && !empty($_POST["Inches"]) && !empty($_POST["Weight"])){

                if(!is_numeric($_POST["Feet"]) || !is_numeric($_POST["Inches"])){
                    $message = "Please enter a valid height.";
                    $this->showCreateAccount($message);
                    return;
                } 
                if(!is_numeric($_POST["Weight"])){
                    $message = "Please enter a valid weight.";
                    $this->showCreateAccount($message);
                    return;
                } 
                if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $passwd)) {
                    $message = "Password must be at least 8 characters long and include at least one lowercase letter, one uppercase letter, and one digit.";
                    $this->showCreateAccount($message);
                    return;
                }
                if($this->checkUserExist($_POST["Email"]) === true){
                    $message = "This email is linked to an existing account. Would you like to log in?";
                    $this->showCreateAccount($message);
                    return;
                }
                if($this->checkUsernameTaken($_POST["Username"]) === true){
                    $message = "This username is already taken. Try something else!";
                    $this->showCreateAccount($message);
                    return;
                } 
                $heightInInches = ($_POST["Feet"] * 12) + $_POST["Inches"];
                $hashedPasswd = password_hash($_POST["Password"], PASSWORD_DEFAULT);
                
                $query = "INSERT INTO ftUsers (name, username, email, passwd, gender, age, height, weight) values ($1, $2, $3, $4, $5, $6, $7, $8) RETURNING userid;";
                $params = [$_POST["Name"], $_POST["Username"], $_POST["Email"], $hashedPasswd,$_POST["Gender"] , $_POST["Age"], $heightInInches , $_POST["Weight"]];
                $result = pg_query_params($this->db, $query, $params);
                if (! $result) {
                    error_log("Create account failed: " . pg_last_error($this->db));
                    $this->showCreateAccount("Database error. Please try again.");
                    return;
                }

                $row = pg_fetch_assoc($result);
                $_SESSION["user_id"] = $row["userid"];

                $_SESSION["name"]     = $_POST["Name"];
                $_SESSION["username"] = $_POST["Username"];
                $_SESSION["email"] = $_POST["Email"];
                $_SESSION["gender"] = $_POST["Gender"];
                $_SESSION["age"] = $_POST["Age"];
                $_SESSION["height"] = $heightInInches;
                $_SESSION["weight"] = $_POST["Weight"];
  
                header("Location: ?command=visitProfile");
                exit;
            }
}



    // Login logic
    public function showLogin($message=""){
        include(__DIR__ . '/login.php'); 
    }

    private function retrieveUser($email){
        $query = "SELECT * FROM ftUsers WHERE email = $1;";
        $result = pg_query_params($this->db, $query, [$email]);
        return pg_fetch_assoc($result) ?: null;
    }
    
    public function login($message=""){
        // if fields are empty, show message
        if (!isset($_POST["Email"]) || !isset($_POST["Password"]) || 
            empty($_POST["Password"]) || empty($_POST["Email"])) {
            $this->showWelcome("Missing information");
            return;
        }

        $email = trim($_POST["Email"]);
        $password = $_POST["Password"];

        // get user row from table by their email
        $user = $this->retrieveUser($email);
        
        // check if user exists in database
        if ($user === null) {
            $this->showWelcome("This email is not connected to an account. Would you like to sign up?");
            return;
        }
        // check if password is identical to hash
        if(password_verify($password, $user["passwd"])){
            $_SESSION["user_id"] = $user["userid"];
            $_SESSION["name"] = $user["name"];
            $_SESSION["email"] = $user["email"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["age"] = $user["age"];
            $_SESSION["gender"] = $user["gender"];
            $_SESSION["weight"] = $user["weight"];
            $_SESSION["height"] = $user["height"];
        }
        else {
            $this-> showWelcome("<p class='alert alert-danger'>Incorrect password!</p>");
            return;
        }
        header("Location: ?command=visitProfile");
        exit;
    }
    public function visitProfile($message = ""){
        echo "hit1";
        $query = "SELECT gender, age, height, weight FROM ftUsers WHERE email = $1";
        $result = pg_query_params($this->db, $query, [$_SESSION["email"]]);
        
        $user = pg_fetch_assoc($result);

        $name = $_SESSION["name"];
        $gender = $user["gender"];
        $age = $user["age"];
        $weight = $user["weight"];
        $heightInInches = $user["height"];
        $inches = $heightInInches % 12;
        $feet = ($heightInInches - $inches) / 12;

        echo "hit2";
        include(__DIR__ . '/profile.php');
        echo "hit3";
    }

    public function showEditProfile($message = ""){
        include(__DIR__ . '/edit-profile.php');
    }

    public function sendUserInfoAPI(){
        $userInfo = [
            "Name" => $_SESSION["name"], 
            "Username" => $_SESSION["username"], 
            "Email" => $_SESSION["email"], 
            "Gender" => $_SESSION["gender"], 
            "Age" => $_SESSION["age"],
            "Height" => $_SESSION["height"], 
            "Weight" => $_SESSION["weight"]
        ];
        header("Content-Type: application/json");
        echo json_encode($userInfo, JSON_PRETTY_PRINT);
            

        }
    }
    
?>
