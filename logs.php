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

if ($_SERVER['REQUEST_METHOD'] === 'GET'
    && isset($_GET['command'])
    && $_GET['command'] === 'activitiesAPI'
) {
    header('Content-Type: application/json; charset=utf-8');

    $res = pg_query_params(
        $dbconn,
        "SELECT activityid, title, activity_type,
                duration_seconds, activity_datetime,
                description, steps, total_distance,
                average_pace, calories_burnt
           FROM activities
          WHERE userid = $1
       ORDER BY activity_datetime DESC",
        [ $_SESSION['user_id'] ]
    );
    $activities = pg_fetch_all($res) ?: [];
    echo json_encode($activities, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    pg_query_params(
        $dbconn,
        "DELETE FROM activities WHERE activityid=$1 AND userid=$2",
        [ (int)$_POST['delete_id'], $_SESSION['user_id'] ]
    );
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
        <a class="btn btn-outline-light me-2" href="goals.php">Goals</a>
        <a class="btn btn-outline-light me-2" href="profile.php">Profile</a>
        <a class="btn btn-outline-light me-2" href="logs.php">Logs</a>
        <a class="btn btn-outline-light" href="logout.php">Logout</a>
      </div>
    </div>
  </nav>

  <div class="container" style="margin-top:5rem;">
    <h1>Your Activity Logs</h1>
    <a href="newActivity.php" class="btn btn-primary mb-3">+ New Activity</a>
    <div id="activities-container" class="row row-cols-1 row-cols-md-2 g-3">
    </div>
  </div>

  <!-- jQuery & AJAX loader -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
  $(function(){
    const $container = $('#activities-container');

    $.getJSON('logs.php?command=activitiesAPI')
      .done(activities => {
        if (!activities.length) {
          $container.html(
            '<div class="alert alert-info">No activities logged yet.</div>'
          );
          return;
        }
        activities.forEach(a => {
          const secs = +a.duration_seconds,
                h = Math.floor(secs/3600),
                m = Math.floor((secs%3600)/60),
                s = secs%60,
                dur = `${h}h ${m}m ${s}s`,
                dt  = new Date(a.activity_datetime)
                         .toLocaleString('en-US', {
                           month: 'numeric',
                           day:   'numeric',
                           year:  'numeric',
                           hour:   'numeric',
                           minute: '2-digit',
                           hour12: true
                         });
          $container.append(`
            <div class="col">
              <div class="card h-100">
                <div class="card-header">
                  <strong>${a.title}</strong>
                  <span class="badge bg-secondary float-end">
                    ${a.activity_type}
                  </span>
                </div>
                <div class="card-body">
                  <p><strong>Date:</strong> ${dt}</p>
                  <p><strong>Duration:</strong> ${dur}</p>
                  ${ a.description
                      ? `<p><strong>Notes:</strong> ${a.description}</p>` 
                      : '' }
                  <ul class="list-unstyled mb-0">
                    ${ a.steps          != null
                        ? `<li>Steps: ${a.steps}</li>` : '' }
                    ${ a.total_distance != null
                        ? `<li>Distance: ${a.total_distance} mi</li>` : '' }
                    ${ a.average_pace   != null
                        ? `<li>Pace: ${a.average_pace} mi/hr</li>` : '' }
                    ${ a.calories_burnt != null
                        ? `<li>Calories: ${a.calories_burnt}</li>` : '' }
                  </ul>
                </div>
                <div class="card-footer text-end">
                  <a href="editActivity.php?id=${a.activityid}"
                     class="btn btn-sm btn-secondary">Edit</a>
                  <form method="POST" action="logs.php" style="display:inline"
                        onsubmit="return confirm('Delete this activity?');">
                    <input type="hidden" name="delete_id"
                           value="${a.activityid}">
                    <button type="submit" class="btn btn-sm btn-danger">
                      Delete
                    </button>
                  </form>
                </div>
              </div>
            </div>
          `);
        });
      })
      .fail((jqxhr, textStatus, error) => {
        console.error("AJAX load error:", textStatus, error);
        $container.html(
          '<div class="alert alert-danger">Could not load activities.</div>'
        );
      });
  });
  </script>
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" 
            integrity="sha384-mQ93r8dhb2uhT9LxeB1Mpr9ZmIdfuK4JbJ8bYvcj0Fow0PHeEq2zYkXf0Ehdc6Bf" 
            crossorigin="anonymous"></script>
</body>
</html>