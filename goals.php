<?php
    // starts the session to retrieve dynamic data (e.g., username from a login session)
    session_start();
    // example: dynamically set the username if it exists
    $userName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Guest';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Fitness Tracker - Goals">
    <meta name="keywords" content="Fitness, Tracker, Goals, Dashboard">
    <meta name="author" content="Olivia Chambers">

    <meta property="og:title" content="Fitness Tracker Goals">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://cs4640.cs.virginia.edu/pnq6th/sprint2/goals.php">
    <meta property="og:description" content="Set and Track Fitness Goals">
    <meta property="og:site_name" content="Fitness Tracker">
    <title>Fitness Tracker - Goals</title>
    <link rel="stylesheet" href="styles/main2.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>
<body>

    <div class="bmi-calculator">
        <h3>BMI Calculator</h3>
        <label for="weight">Weight (lbs):</label>
        <input type="number" id="weight" placeholder="Enter weight">
        <label for="height">Height (in):</label>
        <input type="number" id="height" placeholder="Enter height">
        <button onclick="calculateBMI()">Calculate BMI</button>
        <p class="bmi-result" id="bmiResult"></p>
    </div>

    <div class="fitness-goals">
        <h3 class="goals-header">Fitness Goals</h3>
        <button class="goal-button" onclick="selectGoal('Calorie Deficit')">Calorie Deficit</button>
        <button class="goal-button" onclick="selectGoal('Steps')">Steps</button>
        <button class="goal-button" onclick="selectGoal('Sleep')">Sleep</button>
        <button class="goal-button" onclick="selectGoal('New Goal')">New Goal</button>
    </div>

    <div class="bar-chart-container">
        <canvas id="barChart"></canvas>
    </div>

    
    <nav class="navbar">
        <ul>
            <li><a href="goals.php">Goals</a></li>
            <li><a href="logs.php">Logs</a></li>
            <li><a href="stats.php">Statistics</a></li> 
            <li><a href="profile.php">Profile</a></li>
        </ul>
    </nav>

    <div class="container">
        <main class="content">
            <h1>Fitness Goals</h1>
            <p>Track your fitness goals progress below.</p>

            <section class="statistics">
                <h2>Your Fitness Statistics</h2>
                <canvas id="statsChart" width="1400" height="400"></canvas>
            </section>

        </main>
    </div>

    <script>

      function calculateBMI() {
            var weight = document.getElementById('weight').value;
            var height = document.getElementById('height').value;
            if (weight > 0 && height > 0) {
                var bmi = (weight / (height * height)) * 703;
                document.getElementById('bmiResult').innerText = "Your BMI: " + bmi.toFixed(2);
            } else {
                document.getElementById('bmiResult').innerText = "Please enter valid values.";
            }
        }


        function selectGoal(goal) {
            alert("Selected Goal: " + goal);
        }    

    document.addEventListener("DOMContentLoaded", function () {
        var ctx = document.getElementById("barChart").getContext("2d");

        var barChart = new Chart(ctx, {
            type: "bar",
            data: {
                labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sept", "Oct", "Nov", "Dec"], // X-axis labels
                datasets: [{
                    label: "Activity %",
                    data: [50, 70, 30, 40, 74, 50],
                    backgroundColor: "rgba(75, 192, 192, 0.6)",
                    borderColor: "rgba(75, 192, 192, 1)",
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });
    });

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
