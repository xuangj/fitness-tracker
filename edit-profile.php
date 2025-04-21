<?php
session_start();

if (!isset($_SESSION["username"])) {
    header("Location: ?command=login");
    exit;
}

$host = "localhost"; 
$port = 5432;
$dbname = "pnq6th";
$user = "pnq6th";
$password = "sWYvrJqwKYgB";
/*$host = "db";
$port = "5432";
$dbname = "example";
$user = "localuser";
$password = "cs4640LocalUser!"; */

$dbconn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");
if (!$dbconn) {
    die("Error connecting to the database.");
}

$errors = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name   = trim($_POST["name"]);
    $email  = trim($_POST["email"]);
    $gender = trim($_POST["gender"]);
    $age    = trim($_POST["age"]);
    $weight = trim($_POST["weight"]);
    $height = trim($_POST["height"]);

    if (empty($name)) {
        $errors[] = "Name cannot be empty.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Enter a valid email address.";
    }
    // validates gender: allowed options are "Male", "Female", or "Other"
    $allowedGenders = array("Male", "Female", "Other");
    if (empty($gender) || !in_array($gender, $allowedGenders)) {
        $errors[] = "Please select a valid gender.";
    }
    // validates age: must be numeric and greater than 0
    if (!is_numeric($age) || intval($age) <= 0) {
        $errors[] = "Please enter a valid age.";
    }
    // validates weight: must be numeric and positive
    if (!is_numeric($weight) || floatval($weight) <= 0) {
        $errors[] = "Please enter a valid weight.";
    }
    if (empty($height)) {
        $errors[] = "Height cannot be empty.";
    }

    if (empty($errors)) {
        $query  = "UPDATE users SET name = $1, email = $2, gender = $3, age = $4, weight = $5, height = $6 WHERE username = $7";
        $params = [$name, $email, $gender, intval($age), floatval($weight), $height, $_SESSION["username"]];
        $result = pg_query_params($dbconn, $query, $params);

        if ($result) {
            $_SESSION["name"]  = $name;
            $_SESSION["email"] = $email;
            header("Location: ?command=profile");
            exit;
        } else {
            $errors[] = "Failed to update profile. Please try again.";
        }
    }
} else {
    $query  = "SELECT name, email, gender, age, weight, height FROM users WHERE username = $1";
    $result = pg_query_params($dbconn, $query, [$_SESSION["username"]]);
    $userInfo = pg_fetch_assoc($result);

    $name   = $userInfo["name"]   ?? "";
    $email  = $userInfo["email"]  ?? "";
    $gender = $userInfo["gender"] ?? "";
    $age    = $userInfo["age"]    ?? "";
    $weight = $userInfo["weight"] ?? "";
    $height = $userInfo["height"] ?? "";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Olivia Chambers">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="styles/main2.css">
</head>
<body>
    <div class="edit-container">
        <div class="edit-box">
            <h1>Edit Profile</h1>
            
            <?php if (!empty($errors)): ?>
                <div class="error-messages">
                    <?php foreach ($errors as $error): ?>
                        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <form action="?command=editProfile" method="POST">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required><br>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required><br>

                <label for="gender">Gender:</label>
                <select name="gender" id="gender" required>
                    <option value="">-- Select Gender --</option>
                    <option value="Male" <?php echo ($gender === "Male") ? "selected" : ""; ?>>Male</option>
                    <option value="Female" <?php echo ($gender === "Female") ? "selected" : ""; ?>>Female</option>
                    <option value="Other" <?php echo ($gender === "Other") ? "selected" : ""; ?>>Other</option>
                </select><br>

                <label for="age">Age:</label>
                <input type="number" id="age" name="age" value="<?php echo htmlspecialchars($age); ?>" required><br>

                <label for="weight">Weight (lbs):</label>
                <input type="number" step="any" id="weight" name="weight" value="<?php echo htmlspecialchars($weight); ?>" required><br>

                <label for="height">Height:</label>
                <input type="text" id="height" name="height" value="<?php echo htmlspecialchars($height); ?>" required><br>

                <input type="submit" value="Update Profile">
            </form>
            <br>
            <button onclick="location.href='?command=profile'">Cancel</button>
        </div>
    </div>
</body>
</html>