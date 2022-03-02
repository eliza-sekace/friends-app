<?php

namespace App\Controllers;

use App\Database\Connection;
use App\Views\UsersView;
use App\Views\View;

class UsersController
{
    public function one()
    {
        return new UsersView("Users/one.html", []);
    }

    public function all()
    {
        return new UsersView("Users/all.html", []);
    }

    public function show($vars)
    {
        return new UsersView("Users/show.html", $vars);
    }

    public function login()
    {
        $connection = Connection::connect();
        $user = $connection
            ->fetchAssociative('SELECT * FROM users WHERE 
            email =? OR password =?',
                [
                    'username' => $_GET['username'],
                    'password' => password_hash($_POST['password'], PASSWORD_DEFAULT)
                ]);
        if (count($user) == 0) {
            $user = null;
            header("location: ../index.php?error=usernotfound");
            exit();
        }
        session_start();
        $_SESSION['id'] = $user[0]['id'];
        return new View('Articles/index.php');
    }


    public function signup()
    {
        return new View("Users/signup.html");
    }

    public function store()
    {
        $passwordConfirmation =$_POST['password_confirmation'];
        if ($_POST['password'] !== $passwordConfirmation) {
            return new View("Users/signup.html");
        }
        $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $connection = Connection::connect();
        $connection
            ->insert('users', [
                'email' => $_POST['email'],
                'password' => $hashedPassword
            ]);

        $userId = $connection->lastInsertId();

        $connection = Connection::connect();
        $connection
            ->insert('user_profiles', [
                'user_id' => $userId,
                'name' => $_POST['name'],
                'surname' => $_POST['surname']
            ]);
        return new View("Users/signup.html");
    }


}