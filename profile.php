// olivia chambers
<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
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

$res = pg_query_params(
    $dbconn,
    "SELECT name, username, email, gender, age, height, weight
       FROM ftUsers
      WHERE userid = $1",
    [ $_SESSION['user_id'] ]
);
$user = pg_fetch_assoc($res);
if (!$user) {
    die("User not found.");
}

$name     = htmlspecialchars($user['name']);
$username = htmlspecialchars($user['username']);
$email    = htmlspecialchars($user['email']);
$gender   = htmlspecialchars($user['gender']);
$age      = (int)$user['age'];
$height   = (int)$user['height'];
$feet     = floor($height/12);
$inches   = $height % 12;
$weight   = (float)$user['weight'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="description" content="Fitness Tracker">
    <meta name="keywords" content="Fitness, Tracker, Dashboard, Fitness Tracker">
    <meta name="author" content="Olivia Chambers">

    <meta property="og:title" content="Welcome your Fitness Tracker Dashboard">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://cs4640.cs.virginia.edu/pnq6th/sprint2/index.php">
    <meta property="og:description" content="Dashboard of User">
    <meta property="og:site_name" content="Fitness Tracker">

    <title>User Dashboard</title>
    <link rel="stylesheet" href="styles/main2.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function(){
  const $list = $('#log-list');
  $list.empty();

  $.getJSON('logs.php?command=activitiesAPI')
    .done(activities => {
      if (!activities.length) {
        $list.append('<li>No workouts logged yet.</li>');
        return;
      }
      activities.forEach((a, i) => {
        const dt = new Date(a.activity_datetime)
          .toLocaleDateString('en-US');
        const secs = +a.duration_seconds,
              h = Math.floor(secs/3600),
              m = Math.floor((secs%3600)/60),
              s = secs%60;
        const dur = `${h}h ${m}m ${s}s`;
        const cal = a.calories_burnt || 0;

        $list.append(`
          <li>
            Workout ${i+1}: ${dt} &mdash;
            ${a.activity_type} &mdash;
            ${dur} &mdash;
            ${cal} cals
          </li>
        `);
      });
    })
    .fail(() => {
      $list.html(
        '<li class="text-danger">Could not load workouts.</li>'
      );
    });
});
</script>

</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <div class="profile">
                <h2><?=$name?></h2>
                <p>Gender: <?=$gender?></p>
                <p>Age: <?=$age?></p>
                <p>Weight: <?=$weight?></p>
                <p>Height: <?=$feet?> ' <?=$inches?> "</p>

            </div>
            <nav class="nav-buttons">
                <button onclick="location.href='edit-profile.php'">Edit Profile</button>
                <button onclick="location.href='newActivity.php'">Add Activity</button>
                <button onclick="location.href='goals.php'">Goals</button>
                <!--<button onclick="location.href='logs.php'">Logs</button> -->
                <button id="logs-btn" type="button" class="btn">Logs</button>
                <button onclick="location.href='logout.php'">Logout</button>
            </nav>
        </aside>
        <main class="content">

            <section class="statistics">
                <h2>Your Workouts</h2>
                <canvas id="barChart" width="1700" height="400"></canvas>
            </section>

            <section class="logs">
                <h2>Previous Workouts</h2>
            <ul id="log-list">
            </ul>
            </section>

        </main>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
      const ctx = document.getElementById('barChart').getContext('2d');
      const container = document.querySelector('.bar-chart-container');

      fetch('logs.php?command=activitiesAPI')
        .then(res => {
          if (!res.ok) throw new Error(res.statusText);
          return res.json();
        })
        .then(activities => {
          const monthly = Array(12).fill(0);
          activities.forEach(a => {
            const m = new Date(a.activity_datetime).getMonth();
            monthly[m]++;
          });

          new Chart(ctx, {
            type: 'bar',
            data: {
              labels: ['Jan','Feb','Mar','Apr','May','Jun',
                       'Jul','Aug','Sep','Oct','Nov','Dec'],
              datasets: [{
                label: 'Workouts per Month',
                data: monthly,
                borderWidth: 1
              }]
            },
            options: {
              responsive: true,
              scales: {
                y: { beginAtZero: true, precision: 0 }
              }
            }
          });
        })
        .catch(err => {
          console.error(err);
          container.innerHTML =
            '<p class="text-danger">Could not load activity data for chart.</p>';
        });
    });
    </script>
</body>
</html>
