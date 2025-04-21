// olivia chambers
<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

include 'config.php';

$db = Config::$db;
$host = $db["host"];
$port = $db["port"];
$user = $db["user"];
$pass = $db["pass"];
$database = $db["database"]; 

$this->input = $input;
$this->db = pg_connect("host=$host port=$port dbname=$database user=$user password=$pass");
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
        $query  = "UPDATE ftusers SET name = $1, email = $2, gender = $3, age = $4, weight = $5, height = $6 WHERE username = $7";
        $params = [$name, $email, $gender, intval($age), floatval($weight), $height, $_SESSION["username"]];
        $result = pg_query_params($dbconn, $query, $params);

        if ($result) {
            $_SESSION["name"]  = $name;
            $_SESSION["email"] = $email;
            header("Location: profile.php");
            exit;
        } else {
            $errors[] = "Failed to update profile. Please try again.";
        }
    }
} else {
    $query  = "SELECT name, email, gender, age, weight, height FROM ftusers WHERE username = $1";
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" 
    integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"
    crossorigin="anonymous"/>

</head>
<body>
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-6">

      <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
          <h4 class="mb-0">Edit Profile</h4>
        </div>
        <div class="card-body">
          
          <!-- Error messages -->
          <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
              <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                  <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>

          <form action="edit-profile.php" method="POST" novalidate>
            <div class="mb-3">
              <label for="name" class="form-label">Name</label>
              <input
                type="text"
                id="name"
                name="name"
                class="form-control"
                value="<?= htmlspecialchars($name) ?>"
                required
              >
            </div>

            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input
                type="email"
                id="email"
                name="email"
                class="form-control"
                value="<?= htmlspecialchars($email) ?>"
                required
              >
            </div>

            <div class="mb-3">
              <label for="gender" class="form-label">Gender</label>
              <select
                id="gender"
                name="gender"
                class="form-select"
                required
              >
                <option value="">-- Select Gender --</option>
                <option value="Male"   <?= $gender==='Male'   ? 'selected':'' ?>>Male</option>
                <option value="Female" <?= $gender==='Female' ? 'selected':'' ?>>Female</option>
                <option value="Other"  <?= $gender==='Other'  ? 'selected':'' ?>>Other</option>
              </select>
            </div>

            <div class="row gx-3">
              <div class="col-md-6 mb-3">
                <label for="age" class="form-label">Age</label>
                <input
                  type="number"
                  id="age"
                  name="age"
                  class="form-control"
                  value="<?= htmlspecialchars($age) ?>"
                  required
                >
              </div>
              <div class="col-md-6 mb-3">
                <label for="weight" class="form-label">Weight (lbs)</label>
                <input
                  type="number"
                  step="any"
                  id="weight"
                  name="weight"
                  class="form-control"
                  value="<?= htmlspecialchars($weight) ?>"
                  required
                >
              </div>
            </div>

            <div class="mb-3">
              <label for="height" class="form-label">Height (inches)</label>
              <input
                type="number"
                id="height"
                name="height"
                class="form-control"
                value="<?= htmlspecialchars($height) ?>"
                required
              >
            </div>

            <div class="d-flex justify-content-between">
              <button type="submit" class="btn btn-success">
                Update Profile
              </button>
              <a href="profile.php" class="btn btn-outline-secondary">
                Cancel
              </a>
            </div>
          </form>
          
        </div>
    </div>
  </div>
</div>
<script
  src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
  integrity="sha384-mQ93r8dhb2uhT9LxeB1Mpr9ZmIdfuK4JbJ8bYvcj0Fow0PHeEq2zYkXf0Ehdc6Bf"
  crossorigin="anonymous"></script>
</body>
</html>