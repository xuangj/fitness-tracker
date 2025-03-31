<?php

  $host = "localhost";
    $port = "5432";
    $dbname = "yyf2uf";
    $user = "yyf2uf";
    $password = "mQXFbLeZsW8Z";  
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

    $res = pg_query($dbHandle, "drop sequence if exists user_seq");
    $res = pg_query($dbHandle,"drop table if exists ft_users");

    $res = pg_query($dbHandle, "create sequence user_seq;");

    $res = pg_query($dbHandle, "create table ft_users (
            userId int primary key default nextval ('user_seq'),
            name text,
            username text,
            email text,
            passwd text,
            gender text,
            age int,
            height int,
            weight int);") or die(pg_last_error($dbHandle));

    echo "Done!";