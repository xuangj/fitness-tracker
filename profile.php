<?php
    $userName = "John Doe";  //need to update for actual user name
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

</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <div class="profile">
                <img src="profile.jpg" alt="User Profile Picture">
                <h2><?php echo $userName; ?></h2> <!-- displays dynamic user name -->
                <p>Gender: Male</p>
                <p>Age: 25</p>
                <p>Weight: 160 lbs</p>
                <p>Height: 5'10"</p>
                <button onclick="location.href='edit-profile.php'">Edit Profile</button> <!-- need to create-->
            </div>
            <nav class="nav-buttons">
                <button onclick="location.href='goal.php'">Goals</button>
                <button onclick="location.href='logs.php'">Logs</button> <!-- need to create-->
                <button onclick="location.href='stats.php'">Statistics</button> <!-- need to create-->
            </nav>
        </aside>
        <main class="content">

            <section class="statistics">
                <h2>Your Fitness Statistics</h2>
                <canvas id="statsChart" width="1700" height="400"></canvas>
            </section>

            <section class="logs">
                <h2>Previous Workouts</h2>
                <ul id="log-list">
                    <li>Workout 1: Date - Activity - Duration - Calories</li>
                    <li>Workout 2: Date - Activity - Duration - Calories</li>
                    <li>Workout 3: Date - Activity - Duration - Calories</li>
                </ul>
            </section>

        </main>
    </div>

    <script>
        var ctx = document.getElementById('statsChart').getContext('2d');
        var statsChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                datasets: [{
                    label: 'Weight Progress',
                    data: [160, 158, 155, 153],
                    borderColor: 'rgb(75, 192, 192)',
                    fill: false
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Weeks'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Weight (lbs)'
                        }
                    }
                    }
                }
            });
    </script>
</body>
</html>
