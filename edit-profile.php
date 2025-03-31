<?php
session_start();

// checks if the user is logged in; if not then it will redirect to login page.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// database connection settings
$db_host = "db";
$db_port = "5432";
$db_name = "example";
$db_user = "localuser";
$db_pass = "cs4640LocalUser!";

// database connection using PDO for PostgreSQL
$dsn = "pgsql:host=$db_host;port=$db_port;dbname=$db_name";
try {
    $pdo = new PDO($dsn, $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

$errors = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name   = trim($_POST['name']);
    $email  = trim($_POST['email']);
    $gender = trim($_POST['gender']);
    $age    = intval($_POST['age']);
    $weight = floatval($_POST['weight']);
    $height = trim($_POST['height']);

    if (empty($name) || !preg_match("/^[a-zA-Z ]+$/", $name)) {
        $errors[] = "Please enter a valid name (letters and spaces only).";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }
    $allowedGenders = array("Male", "Female", "Other");
    if (empty($gender) || !in_array($gender, $allowedGenders)) {
        $errors[] = "Please select a valid gender.";
    }
    if ($age < 1 || $age > 120) {
        $errors[] = "Please enter a valid age.";
    }
    if ($weight <= 0) {
        $errors[] = "Please enter a valid weight.";
    }
    if (empty($height)) {
        $errors[] = "Please enter a valid height.";
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE users SET name = :name, email = :email, gender = :gender, age = :age, weight = :weight, height = :height WHERE id = :id");
        $stmt->execute([
            ':name'   => $name,
            ':email'  => $email,
            ':gender' => $gender,
            ':age'    => $age,
            ':weight' => $weight,
            ':height' => $height,
            ':id'     => $_SESSION['user_id']
        ]);
        $_SESSION['message'] = "Profile updated successfully.";
        header("Location: profile.php");
        exit;
    }
} else {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute([':id' => $_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("User not found.");
    }

    $name   = $user['name'];
    $email  = $user['email'];
    $gender = $user['gender'];
    $age    = $user['age'];
    $weight = $user['weight'];
    $height = $user['height'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="styles/main2.css">
</head>
<body>
    <div class="container">
        <h1>Edit Profile</h1>
        <?php if (!empty($errors)): ?>
            <div class="error-messages">
                <?php foreach ($errors as $error): ?>
                    <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <form action="edit-profile.php" method="POST">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required><br>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required><br>

            <label for="gender">Gender:</label>
            <select name="gender" id="gender" required>
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
    </div>
</body>
</html>
