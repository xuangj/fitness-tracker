<!--Xuang Jin-->

<?php
    //include 'config.php';

    // for server
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
    
    $dbHandle = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

    if ($dbHandle) {
        echo "Success connecting to database<br>\n";
    } else {
        die("An error occurred connecting to the database");
    }

    pg_query($dbHandle, "drop table if exists ftUsers;");
    pg_query($dbHandle, "drop sequence if exists ftUser_seq;");

    $res  = pg_query($dbHandle, "create sequence ftUser_seq;");

    $res = pg_query($dbHandle, "create table ftUsers (
            userId serial primary key,
            name text,
            username text,
            email text,
            passwd text,
            gender text,
            age int,
            height int,
            weight int);") or die(pg_last_error($dbHandle));

    $res =  pg_query($dbHandle, "CREATE TABLE activities (
            activityId SERIAL PRIMARY KEY,
            userId INTEGER NOT NULL REFERENCES ftUsers(userId) ON DELETE CASCADE,
            title TEXT NOT NULL,
            activity_type TEXT NOT NULL,
            duration_seconds INTEGER NOT NULL,
            activity_datetime TIMESTAMP NOT NULL,
            description TEXT,
            steps INTEGER,
            total_distance REAL,
            average_pace REAL,
            calories_burnt INTEGER);") or die("activities table creation failed: " . pg_last_error($dbHandle));
    

    echo "Done!";