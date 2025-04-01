<!DOCTYPE html>
<html lang="en">
   
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="author" content="Xuang Jin">
        <meta name="description" content="Create Account scene">
        <meta name="keywords" content="Create Account">

        <meta property="og:title" content="Create Account">
        <meta property="og:type" content="website">
        <meta property="og:url" content="https://cs4640.cs.virginia.edu/pnq6th/sprint2/create-account.html">
        <meta property="og:description" content="Make a new account">
        <meta property="og:site_name" content="Create Account">
    
        <!--Sources:
        https://stackoverflow.com/questions/40818684/how-can-i-make-my-buttons-apear-inline-next-to-text
        https://www.w3schools.com/css/css3_shadows.asp
        https://www.w3schools.com/cssref/css3_pr_justify-content.php
        -->

        <title>Create Account Scene</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"  integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"  crossorigin="anonymous"> 
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <link rel="stylesheet" href="styles/main.css">
    </head>
    
    <body>
        <!--Navigation bar includes web info link, Log In, and Join button. Collapsible depending on screen size -->
        <nav class="navbar navbar-dark bg-dark navbar-expand-md" aria-label="Main Navigation Bar">
            <div class="container-xl">
                <a class="navbar-brand" href="#" style="font-size:x-large"> LOGO</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <div class="ms-auto d-flex align-items-center">
                        <button class="btn me-auto" type="button" style="color:  white; font-size:larger"><u>About Us</u></button>
                        <a class="btn btn-primary" href="login.php">Log In</a>
                        <p class="mb-0 mx-2" style="color:white"> or </p>
                        <a class="btn btn-primary" href="createaccount.php">Join for Free!</a>
                    </div>
                </div>
            </div>
        </nav>

        <form action="index.php?command=createAccount" method="POST">
        <div class="container py-5">
            <div class="row justify-content-center">
                <!-- Login Information Card -->
                <div class="col-md-5">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h2 class="card-title text-center mb-4">Join Us!</h2>

                            <div class="mb-3">
                                <label for="Name">Name</label>
                                <input type="text" class="form-control" id="Name" name="Name">
                            </div>

                            <div class="mb-3">
                                <label for="Email">Email</label>
                                <input type="email" class="form-control" id="Email" name="Email">
                            </div>

                            <div class="mb-3">
                                <label for="Username">Username</label>
                                <input type="text" class="form-control" id="Username" name="Username">
                            </div>

                            <div class="mb-3">
                                <label for="Password">Password</label>
                                <input type="password" class="form-control" id="Password" name="Password">
                            </div>

                            <p style="font-size:10pt">Passwords must be at least 8 characters long.</p>
                        </div>
                    </div>
                </div>

                <!-- Physical Information Card -->
                <div class="col-md-5">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h2 class="card-title text-center mb-4">Physical Information</h2>

                            <div class="mb-3">
                                <label for="Gender">Gender</label>
                                <select class="form-control" name="Gender" id="Gender">
                                    <option value="preferNotToSay">Prefer Not to Say</option>
                                    <option value="female">Female</option>
                                    <option value="male">Male</option>
                                    <option value="nonbinary">Nonbinary</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="age">Age</label>
                                <div class="input-group">
                                    <input id="Age" type="text" class="form-control" name="Age" placeholder="0">
                                    <span class="input-group-text">Years</span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="Height">Height</label>
                                <div class="input-group">
                                    <input id="Feet" type="text" class="form-control" name="Feet" placeholder="0">
                                    <span class="input-group-text">feet</span>
                                    <input id="Inches" type="text" class="form-control" name="Inches" placeholder="00">
                                    <span class="input-group-text">inches</span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="Weight">Weight</label>
                                <div class="input-group">
                                    <input id="Weight" type="text" class="form-control" name="Weight" placeholder="0">
                                    <span class="input-group-text">pounds</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="row justify-content-center mt-4">
                <div class="col-md-10 text-center">
                    <button type="submit" class="btn btn-primary">Sign Up</button>
                </div>
            </div>
        </div>
    </form>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" 
        integrity="sha384-mQ93r8dhb2uhT9LxeB1Mpr9ZmIdfuK4JbJ8bYvcj0Fow0PHeEq2zYkXf0Ehdc6Bf" 
        crossorigin="anonymous"></script>

    </body>


</html>