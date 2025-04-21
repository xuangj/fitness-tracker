// olivia chambers
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

$dbconn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password") or die("DB connect error");

$error     = "";
$activity  = null;

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  if (empty($_GET['id'])) {
    header("Location: logs.php");
    exit;
  }
  $id = (int) $_GET['id'];
  $res = pg_query_params(
    $dbconn,
    "SELECT * FROM activities WHERE activityid=$1 AND userid=$2",
    [ $id, $_SESSION['user_id'] ]
  );
  if (!$res || pg_num_rows($res) === 0) {
    header("Location: logs.php");
    exit;
  }
  $activity = pg_fetch_assoc($res);

  $dtObj = new DateTime($activity['activity_datetime']);
  $date  = $dtObj->format('m/d/Y');
  $time  = $dtObj->format('h:i');
  $ampm  = $dtObj->format('A');

  $secs = (int)$activity['duration_seconds'];
  $h = floor($secs/3600);
  $m = floor(($secs%3600)/60);
  $s = $secs % 60;

  $title         = $activity['title'];
  $activityType  = $activity['activity_type'];
  $description   = $activity['description'];
  $steps         = $activity['steps'];
  $totalDistance = $activity['total_distance'];
  $averagePace   = $activity['average_pace'];
  $caloriesBurnt = $activity['calories_burnt'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id             = (int) $_GET['id'];
  $title          = trim($_POST['Title']);
  $activityType   = trim($_POST['activityType']);
  $h              = (int) trim($_POST['durationHours']);
  $m              = (int) trim($_POST['durationMinutes']);
  $s              = (int) trim($_POST['durationSeconds']);
  $date           = trim($_POST['date']);
  $time           = trim($_POST['time']);
  $ampm           = trim($_POST['ampm']);
  $description    = trim($_POST['description']);
  $steps          = trim($_POST['steps']);
  $totalDistance  = trim($_POST['totalDistance']);
  $averagePace    = trim($_POST['averagePace']);
  $caloriesBurnt  = trim($_POST['caloriesBurnt']);

  if ($title === "" || $activityType === "") {
    $error = "Title and activity type are required.";
  } else {
    $durationSeconds = $h*3600 + $m*60 + $s;
    $dtString        = "$date $time $ampm";
    $activityDT      = date("Y-m-d H:i:s", strtotime($dtString));

    $sql = "UPDATE activities SET title = $1, activity_type = $2, duration_seconds = $3, activity_datetime = $4, description = $5, steps = $6, total_distance = $7, average_pace = $8, calories_burnt = $9 WHERE activityid = $10 AND userid = $11";
    $params = [
      $title,
      $activityType,
      $durationSeconds,
      $activityDT,
      $description   ?: null,
      is_numeric($steps)          ? (int)$steps          : null,
      is_numeric($totalDistance)  ? (float)$totalDistance: null,
      is_numeric($averagePace)    ? (float)$averagePace  : null,
      is_numeric($caloriesBurnt)  ? (int)$caloriesBurnt  : null,
      $id,
      $_SESSION['user_id']
    ];
    $upd = pg_query_params($dbconn, $sql, $params);
    if ($upd) {
      header("Location: logs.php");
      exit;
    } else {
      $error = "Failed to update: " . pg_last_error($dbconn);
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Activity</title>
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
    rel="stylesheet"
  >
      <link rel="stylesheet" href="styles/main2.css">

</head>
<body class="p-4">
  <nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container-fluid">
      <a class="navbar-brand" href="logs.php">Fitness Tracker</a>
      <div>
        <a class="btn btn-outline-light me-2" href="goals.php">Goals</a>
        <a class="btn btn-outline-light me-2" href="profile.php">Profile</a>
        <a class="btn btn-outline-light" href="logout.php">Logout</a>
      </div>
    </div>
  </nav>

  <h1><?= $_SERVER['REQUEST_METHOD']==='GET' ? 'Edit Activity' : 'Fix Errors' ?></h1>
  <?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST" action="editActivity.php?id=<?= $id ?>">
    <div class="mb-3">
      <label for="Title" class="form-label">Title</label>
      <input 
        id="Title" name="Title" type="text"
        class="form-control"
        value="<?= htmlspecialchars($title) ?>"
        required
      >
    </div>
    <div class="mb-3">
      <label for="activityType" class="form-label">Activity Type</label>
      <select id="activityType" name="activityType" class="form-select" required>
        <option value="">-- Select --</option>
        <?php 
          $types = ['Running','Weight Training','Swimming','Biking','Rock-climbing','Pickleball'];
          foreach ($types as $t): 
        ?>
          <option 
            value="<?= $t ?>"
            <?= $t === $activityType ? 'selected' : '' ?>
          ><?= $t ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="mb-3">
      <label class="form-label">Duration</label>
      <div class="input-group">
        <input name="durationHours"   class="form-control" value="<?= $h ?>" placeholder="hh">
        <span class="input-group-text">hr</span>
        <input name="durationMinutes" class="form-control" value="<?= $m ?>" placeholder="mm">
        <span class="input-group-text">min</span>
        <input name="durationSeconds" class="form-control" value="<?= $s ?>" placeholder="ss">
        <span class="input-group-text">sec</span>
      </div>
    </div>
    <div class="mb-3">
      <label class="form-label">Date &amp; Time</label>
      <div class="input-group">
        <input name="date" class="form-control" value="<?= $date ?>" placeholder="MM/DD/YYYY" required>
        <input name="time" class="form-control" value="<?= $time ?>" placeholder="HH:MM" required>
        <select name="ampm" class="form-select" required>
          <option <?= $ampm==='AM'?'selected':'' ?>>AM</option>
          <option <?= $ampm==='PM'?'selected':'' ?>>PM</option>
        </select>
      </div>
    </div>
    <div class="mb-3">
      <label class="form-label">Description</label>
      <input name="description" class="form-control"
             value="<?= htmlspecialchars($description) ?>">
    </div>
    <div class="row g-3 mb-3">
      <div class="col"><label>Steps</label>
        <input name="steps" class="form-control" value="<?= $steps ?>"></div>
      <div class="col"><label>Distance</label>
        <input name="totalDistance" class="form-control" value="<?= $totalDistance ?>"></div>
      <div class="col"><label>Pace</label>
        <input name="averagePace" class="form-control" value="<?= $averagePace ?>"></div>
      <div class="col"><label>Calories</label>
        <input name="caloriesBurnt" class="form-control" value="<?= $caloriesBurnt ?>"></div>
    </div>
    <button type="submit" class="btn btn-primary">Save Changes</button>
    <a href="logs.php" class="btn btn-link text-danger">Cancel</a>
  </form>

  <script 
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
  ></script>
</body>
</html>
