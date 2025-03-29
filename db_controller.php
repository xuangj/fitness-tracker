<?php

    $host = "db";
    $port = "5432";
    $db = "example";
    $user = "localuser";
    $password = "cs4640LocalUser!"; 
    
    $dbHandle = pg_connect("host=$host port=$port dbname=$db user=$user password=$password");

    if ($dbHandle) {
        echo "Success connecting to database<br>\n";
    } else {
        die("An error occurred connecting to the database");
    }

    $res = pg_query($dbHandle, "drop sequence if exists user_seq");
    $res = pg_query($dbHandle,"drop table if exists user");

    $res = pg_query($dbHandle, "create sequence user_seq;");

    $res = pg_query($dbHandle, "create table users (
            userId int primary key default nextval ('user_seq'),
            name text,
            username text,
            email text,
            password text);") or die(pg_last_error($dbHandle));

    echo "Done!";