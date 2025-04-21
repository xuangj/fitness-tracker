//olivia chambers
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
    <nav class="navbar">
        <ul>
            <li><a href="newActivity.php">Activity</a></li>
            <li><a href="goals.php">Goals</a></li>
            <li><a href="logs.php">Logs</a></li>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="container">
        <main class="content">
            <h1>Fitness Goals</h1>
            <p>Track your fitness goals progress below.</p>

        </main>
    </div>
    
    <div class="goals-layout">
        <div class="bmi-calculator">
            <h3>BMI Calculator</h3>
            <label for="weight">Weight (lbs):</label>
            <input type="number" id="weight" placeholder="Enter weight">
            <label for="height">Height (in):</label>
            <input type="number" id="height" placeholder="Enter height">
            <button onclick="calculateBMI()">Calculate BMI</button>
            <p class="bmi-result" id="bmiResult"></p>
        </div>

        <div class="bar-chart-container">
            <canvas id="barChart"></canvas>
        </div>
    </div>

    <script>
    class BMI {                         //javascript object 
      constructor(weight, height) {
        this.weight = weight;
        this.height = height;
      }
      calculate() {
        return (this.weight / (this.height * this.height)) * 703;
      }
      category(bmi) {
        if (bmi < 18.5) return 'Underweight';
        if (bmi < 25)   return 'Normal';
        if (bmi < 30)   return 'Overweight';
        return 'Obese';
      }
    }

    document.querySelector('.bmi-calculator button')
            .addEventListener('click', () => {
      const w = parseFloat(document.getElementById('weight').value);
      const h = parseFloat(document.getElementById('height').value);
      const output = document.getElementById('bmiResult');
      if (w > 0 && h > 0) {
        const bmiObj = new BMI(w, h);
        const val    = bmiObj.calculate();
        const cat    = bmiObj.category(val);
        output.innerText = `Your BMI: ${val.toFixed(1)} (${cat})`;
      } else {
        output.innerText = 'Please enter valid values.';
      }
    });
    </script>

  
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

    </script>
</body>
</html>