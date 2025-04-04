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

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title           = trim($_POST["Title"]);
    $activityType    = trim($_POST["activityType"]);
    $durationHours   = trim($_POST["durationHours"]);
    $durationMinutes = trim($_POST["durationMinutes"]);
    $durationSeconds = trim($_POST["durationSeconds"]);
    $date            = trim($_POST["date"]);
    $time            = trim($_POST["time"]);
    $ampm            = trim($_POST["ampm"]);
    $description     = trim($_POST["description"]);
    $steps           = trim($_POST["steps"]);
    $totalDistance   = trim($_POST["totalDistance"]);
    $averagePace     = trim($_POST["averagePace"]);
    $caloriesBurnt   = trim($_POST["caloriesBurnt"]);

    $durationInSeconds = ($durationHours * 3600) + ($durationMinutes * 60) + ($durationSeconds);

    $dateTimeStr = $date . " " . $time . " " . $ampm;
    $activityDateTime = date("Y-m-d H:i:s", strtotime($dateTimeStr));

    if (empty($title) || empty($activityType)) {
         $error = "Title and activity type are required.";
    } else {
         $query = "INSERT INTO activities 
                     (user_id, title, activity_type, duration_seconds, activity_datetime, description, steps, total_distance, average_pace, calories_burnt)
                   VALUES 
                     ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10)";
         $params = [
              $_SESSION['user_id'], // might need to change to correct variable
              $title,
              $activityType,
              $durationInSeconds,
              $activityDateTime,
              $description,
              $steps,
              $totalDistance,
              $averagePace,
              $caloriesBurnt
         ];

         $result = pg_query_params($dbconn, $query, $params);

         if ($result) {
             header("Location: logs.php");
             exit;
         } else {
             $error = "Error saving activity. Please try again.";
         }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="New Activity scene">
    <meta name="keywords" content="New Activity">
    <meta name="author" content="Olivia Chambers">
    <meta property="og:title" content="New Activity">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://cs4640.cs.virginia.edu/pnq6th/sprint2/newActivity.php">
    <meta property="og:description" content="Log and detail new activity">
    <meta property="og:site_name" content="New Activity">
    <title>New Activity Scene</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="styles/main.css">
</head>
<body>
    <nav class="navbar navbar-dark bg-dark navbar-expand-md" aria-label="Main Navigation Bar">
        <div class="container-xl">
            <a class="navbar-brand" href="#" style="font-size:x-large">LOGO</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="ms-auto d-flex align-items-center">
                    <button class="btn me-auto" type="button" style="color: white; font-size:larger"><u>Activity Feed</u></button>
                    <a class="btn" href="newActivity.php" id="addButton" aria-label="Add new activity">
                        <i class="fa-solid fa-circle-plus"></i>
                    </a>
                    <a class="btn" href="profile.php" id="profileButton" aria-label="User profile">
                        <i class="fa-regular fa-circle-user"></i>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container" style="background-color:rgb(227, 227, 227); margin-top:30px;">
        <h1>New Activity</h1>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form action="newActivity.php" method="POST">
            <!-- Title -->
            <div class="mb-3">
                <label for="Title" class="form-label">Title</label>
                <input id="Title" name="Title" type="text" placeholder="New Title" class="form-control" required>
            </div>
            <!-- Activity Type -->
            <div class="mb-3">
                <label for="activityType" class="form-label">Activity Type</label>
                <select id="activityType" name="activityType" class="form-select" required>
                    <option value="">-- Select Activity --</option>
                    <option value="Running">Running</option>
                    <option value="Weight Training">Weight Training</option>
                    <option value="Swimming">Swimming</option>
                    <option value="Biking">Biking</option>
                    <option value="Rock-climbing">Rock-climbing</option>
                    <option value="Pickleball">Pickleball</option>
                </select>
            </div>
            <!-- Duration -->
            <div class="mb-3">
                <label class="form-label">Duration</label>
                <div class="input-group">
                    <input id="durationHours" name="durationHours" type="text" class="form-control" placeholder="00">
                    <span class="input-group-text">hr</span>
                    <input id="durationMinutes" name="durationMinutes" type="text" class="form-control" placeholder="00">
                    <span class="input-group-text">min</span>
                    <input id="durationSeconds" name="durationSeconds" type="text" class="form-control" placeholder="00">
                    <span class="input-group-text">sec</span>
                </div>
            </div>
            <!-- Date & Time -->
            <div class="mb-3">
                <label class="form-label">Date &amp; Time</label>
                <div class="input-group">
                    <input id="date" name="date" type="text" placeholder="MM/DD/YYYY" class="form-control" required>
                    <input id="time" name="time" type="text" placeholder="HH:MM" class="form-control" required>
                    <select id="ampm" name="ampm" class="form-select" required>
                        <option value="AM">AM</option>
                        <option value="PM">PM</option>
                    </select>
                </div>
            </div>
            <!-- Description -->
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <input type="text" id="description" name="description" placeholder="How did your workout feel? Tell us more..." class="form-control">
            </div>
            <!-- Quantitative Data -->
            <div class="mb-3">
                <label for="steps" class="form-label">Steps</label>
                <input id="steps" name="steps" type="text" placeholder="0 steps" class="form-control">
            </div>
            <div class="mb-3">
                <label for="totalDistance" class="form-label">Total Distance</label>
                <input id="totalDistance" name="totalDistance" type="text" placeholder="0 miles" class="form-control">
            </div>
            <div class="mb-3">
                <label for="averagePace" class="form-label">Average Pace</label>
                <input id="averagePace" name="averagePace" type="text" placeholder="0 mi/hr" class="form-control">
            </div>
            <div class="mb-3">
                <label for="caloriesBurnt" class="form-label">Calories Burnt</label>
                <input id="caloriesBurnt" name="caloriesBurnt" type="text" placeholder="0 cals" class="form-control">
            </div>
            <!-- Form Buttons -->
            <div class="mb-3">
                <button class="btn btn-primary" type="submit" id="CreatActivityButton">Create Activity</button>
                <a class="btn btn-link" href="profile.php" id="cancelButton" style="color:red">Cancel</a>
            </div>
        </form>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" 
            integrity="sha384-mQ93r8dhb2uhT9LxeB1Mpr9ZmIdfuK4JbJ8bYvcj0Fow0PHeEq2zYkXf0Ehdc6Bf" 
            crossorigin="anonymous"></script>
</body>
</html>
