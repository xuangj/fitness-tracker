<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
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

$sql = " SELECT activityid, title, activity_type, duration_seconds, activity_datetime, description, steps, total_distance, average_pace, calories_burnt FROM activities WHERE userid = $1 ORDER BY activity_datetime DESC";
$result = pg_query_params($dbconn, $sql, [$_SESSION['user_id']]);
if (! $result) {
    die("Database error: " . pg_last_error($dbconn));
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $deleteId = (int) $_POST['delete_id'];
    $res = pg_query_params(
        $dbconn,
        "DELETE FROM activities WHERE activityid = $1 AND userid = $2",
        [ $deleteId, $_SESSION['user_id'] ]
    );
    if (! $res) {
        error_log("Failed to delete activity #{$deleteId}: " . pg_last_error($dbconn));
    }
    header("Location: logs.php");
    exit;
}


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
    <link rel="stylesheet" href="styles/main2.css">

</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
      <a class="navbar-brand" href="profile.php">Fitness Tracker</a>
      <div>
        <a class="btn btn-outline-light me-2" href="newActivity.php">Add Activity</a>
        <a class="btn btn-outline-light me-2" href="goals.php">Goals</a>
        <a class="btn btn-outline-light me-2" href="profile.php">Profile</a>
        <a class="btn btn-outline-light me-2" href="logs.php">Logs</a>
        <a class="btn btn-outline-light"        href="logout.php">Logout</a>
      </div>
    </div>
  </nav>

  <div class="container" style="margin-top:5rem;">
    <h1>Your Activity Logs</h1>
    <a href="newActivity.php" class="btn btn-primary mb-3">+ New Activity</a>

    <?php if (pg_num_rows($result) === 0): ?>
      <div class="alert alert-info">No activities logged yet.</div>
    <?php else: ?>
      <div class="row row-cols-1 row-cols-md-2 g-3">
        <?php while ($row = pg_fetch_assoc($result)): 
          $secs = (int)$row['duration_seconds'];
          $h = floor($secs/3600);
          $m = floor(($secs%3600)/60);
          $s = $secs % 60;
          $duration = sprintf("%02dh %02dm %02ds", $h, $m, $s);
          $dt = date("n/j/Y g:i A", strtotime($row['activity_datetime']));
        ?>
        <div class="col">
          <div class="card h-100">
            <div class="card-header">
              <strong><?= htmlspecialchars($row['title']) ?></strong>
              <span class="badge bg-secondary float-end">
                <?= htmlspecialchars($row['activity_type']) ?>
              </span>
            </div>
            <div class="card-body">
              <p><strong>Date:</strong> <?= $dt ?></p>
              <p><strong>Duration:</strong> <?= $duration ?></p>
              <?php if ($row['description']): ?>
                <p><strong>Notes:</strong> <?= htmlspecialchars($row['description']) ?></p>
              <?php endif; ?>
              <ul class="list-unstyled mb-0">
                <?php if ($row['steps'] !== null): ?>
                  <li>Steps: <?= (int)$row['steps'] ?></li>
                <?php endif; ?>
                <?php if ($row['total_distance'] !== null): ?>
                  <li>Distance: <?= (float)$row['total_distance'] ?> miles</li>
                <?php endif; ?>
                <?php if ($row['average_pace'] !== null): ?>
                  <li>Pace: <?= (float)$row['average_pace'] ?> mi/hr</li>
                <?php endif; ?>
                <?php if ($row['calories_burnt'] !== null): ?>
                  <li>Calories: <?= (int)$row['calories_burnt'] ?></li>
                <?php endif; ?>
              </ul>
            </div>
            <div class="card-footer text-end">
              <!-- edit link -->
              <a 
                href="editActivity.php?id=<?= $row['activityid'] ?>" 
                class="btn btn-sm btn-secondary"
              >Edit</a>

            <form method="POST" style="display:inline" onsubmit="return confirm('Delete this activity?');">
            <input type="hidden" name="delete_id" value="<?= $row['activityid'] ?>">
            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
            </form>
            </div>
          </div>
        </div>
        <?php endwhile; ?>
      </div>
    <?php endif; ?>
  </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" 
            integrity="sha384-mQ93r8dhb2uhT9LxeB1Mpr9ZmIdfuK4JbJ8bYvcj0Fow0PHeEq2zYkXf0Ehdc6Bf" 
            crossorigin="anonymous"></script>
</body>
</html>