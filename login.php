<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="author" content="Xuang Jin">
        <meta name="description" content="Log in scene">
        <meta name="keywords" content="Log in">

        <meta property="og:title" content="Log In">
        <meta property="og:type" content="website">
        <meta property="og:url" content="https://cs4640.cs.virginia.edu/pnq6th/sprint2/log-in.html">
        <meta property="og:description" content="Log in with credentials">
        <meta property="og:site_name" content="Log In">

        <title>Log-In Scene</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"  integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"  crossorigin="anonymous"> 
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <link rel="stylesheet" href="styles/main.css">

        <!--Sources:
        https://www.w3schools.com/css/css3_shadows.asp
        https://www.w3schools.com/cssref/css3_pr_justify-content.php
        -->
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

        <div class="py-5 mb-4">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-4 col-12"  id="LoginContainer">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h1 class="card-title text-center mb-4" style="padding:10px">Welcome Back!</h1>
                                <form action="index.php?command=login" method="POST">
                                    <?=$message?></br>
                                    <!--Allows user to log into existing account using set username and password-->
                                    <label for="Email">Email</label>
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control LogInInfo" id="Email" aria-describedby="emailHelp" name="Email">
                                    </div>
            
                                    <label for="Password">Password</label>
                                    <div class="input-group mb-3">
                                        <input type="password" class="form-control LogInInfo" input id="Password" name="Password">
                                    </div>
                                    <button class="btn " type="button" id="forgotButton"><u>Forgot password?</u></button>
                                    <div class="d-flex flex-column align-items-center"> 
                                        <button type="submit" class="btn btn-primary">Log In</button>
                                        <p class="mb-0 mt-0" style="color:white">OR</p>
                                        <!--Allows user to create new account-->
                                        <a class="btn btn-primary" type="button" href="createAccount.php" id="CreateAccountButton">Create an Account</a>
                                    </div> 
                                </form>
                            </div>
                        </div> 
                    </div> 
                </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" 
        integrity="sha384-mQ93r8dhb2uhT9LxeB1Mpr9ZmIdfuK4JbJ8bYvcj0Fow0PHeEq2zYkXf0Ehdc6Bf" 
        crossorigin="anonymous"></script>
    </body>
</html>
