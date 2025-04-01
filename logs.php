<?php
session_start();

if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit;
} 

$host     = "db";
$port     = "5432";
$dbname   = "example";
$dbuser   = "localuser";
$dbpass   = "cs4640LocalUser!";

$dbconn = pg_connect("host=$host port=$port dbname=$dbname user=$dbuser password=$dbpass");
if (!$dbconn) {
    die("Error connecting to the database.");
} 

$query = "SELECT * FROM activities WHERE user_id = $1 ORDER BY activity_datetime DESC";
$result = pg_query_params($dbconn, $query, [$_SESSION['user_id']]);
$activities = pg_fetch_all($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Olivia Chambers">
    <title>Your Activity Logs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" 
          integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" 
          crossorigin="anonymous">
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="profile.php">Fitness Tracker</a>
            <div>
                <a class="btn btn-outline-light me-2" href="newActivity.php">Add Activity</a>
                <a class="btn btn-outline-light" href="goals.php">Goals</a>
                <a class="btn btn-outline-light" href="profile.php">Profile</a>
                <a class="btn btn-outline-light" href="logs.php">Logs</a>
                <a class="btn btn-outline-light" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">
        <h1>Your Activity Logs</h1>
        <?php if (empty($activities)): ?>
            <p>No activities found. <a href="newActivity.php">Add a new activity</a>.</p>
        <?php else: ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Activity Type</th>
                        <th>Date &amp; Time</th>
                        <th>Duration (sec)</th>
                        <th>Description</th>
                        <th>Steps</th>
                        <th>Total Distance</th>
                        <th>Average Pace</th>
                        <th>Calories Burnt</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($activities as $activity): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($activity['title']); ?></td>
                            <td><?php echo htmlspecialchars($activity['activity_type']); ?></td>
                            <td><?php echo htmlspecialchars($activity['activity_datetime']); ?></td>
                            <td><?php echo htmlspecialchars($activity['duration_seconds']); ?></td>
                            <td><?php echo htmlspecialchars($activity['description']); ?></td>
                            <td><?php echo htmlspecialchars($activity['steps']); ?></td>
                            <td><?php echo htmlspecialchars($activity['total_distance']); ?></td>
                            <td><?php echo htmlspecialchars($activity['average_pace']); ?></td>
                            <td><?php echo htmlspecialchars($activity['calories_burnt']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" 
            integrity="sha384-mQ93r8dhb2uhT9LxeB1Mpr9ZmIdfuK4JbJ8bYvcj0Fow0PHeEq2zYkXf0Ehdc6Bf" 
            crossorigin="anonymous"></script>
</body>
</html>