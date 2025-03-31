<?php
session_start();

// i'll need to change based on how we are retrieving the user data
$userName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : '';
$userEmail = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : '';
$userPhone = isset($_SESSION['user_phone']) ? $_SESSION['user_phone'] : '';

// checks if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newName = htmlspecialchars($_POST['name']);
    $newEmail = htmlspecialchars($_POST['email']);
    $newPhone = htmlspecialchars($_POST['phone']);
    

    $_SESSION['user_name'] = $newName;
    $_SESSION['user_email'] = $newEmail;
    $_SESSION['user_phone'] = $newPhone;

    header('Location: profile.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Edit Profile - Fitness Tracker">
    <meta name="keywords" content="Profile, Edit, Fitness Tracker">
    <meta name="author" content="Olivia Chambers">
    <title>Edit Profile - Fitness Tracker</title>
    <link rel="stylesheet" href="styles/main2.css">
</head>
<body>

    <div class="container">
        <main class="content">
            <h1>Edit Profile</h1>
            <form action="edit-profile.php" method="POST">
                <div class="form-group">
                    <label for="name">Full Name:</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($userName); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($userEmail); ?>" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone:</label>
                    <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($userPhone); ?>" required>
                </div>
                <button type="submit">Save Changes</button>
            </form>
        </main>
    </div>

    <nav class="navbar">
        <ul>
            <li><a href="goals.php">Goals</a></li>
            <li><a href="logs.php">Logs</a></li>
            <li><a href="stats.php">Statistics</a></li>
            <li><a href="profile.php">Profile</a></li>
        </ul>
    </nav>

</body>
</html>
