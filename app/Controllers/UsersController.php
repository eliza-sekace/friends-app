<?php

namespace App\Controllers;

use App\Database\Connection;
use App\Views\UsersView;
use App\Views\View;
use App\Models\Profile;

class UsersController
{
    public function one()
    {
        return new UsersView("Users/one.html", []);
    }

    public function all()
    {
        $connection = Connection::connect();
        $results = $connection
            ->createQueryBuilder()
            ->select('id', 'user_id', 'name', 'surname')
            ->from('user_profiles')
            ->orderBy('id', "desc")
            ->executeQuery()
            ->fetchAllAssociative();

        $users = [];
        foreach ($results as $user) {
            $users[] = new Profile($user['id'], $user['user_id'], $user['name'], $user['surname']);
        }
        //get all users, who are not friends
        $connection = Connection::connect();

        $qb = $connection->createQueryBuilder();
        $subQb = $connection->createQueryBuilder();

        $subquery = $subQb
            ->select('user_id')
            ->from('friends')
            ->where('friends_id = ?');

        $query = $qb
            ->select('f.friends_id', 'p.name', 'p.surname')
            ->from('friends', 'f')
            ->leftJoin('f', 'user_profiles', 'p', 'f.friends_id = p.user_id')
            ->where('f.user_id = ?')
            ->andWhere($qb->expr()->notIn('f.friends_id', $subquery->getSQL()))
            ->setParameter(0, $_SESSION['user_id'])
            ->setParameter(1, $_SESSION['user_id']);



        return new View("Users/all.html", [
            'users' => $users,
            'notFriends' => $query->fetchAllAssociative(),
            'currentUser'=>$_SESSION['user_id']
        ]);
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
        $passwordConfirmation = $_POST['password_confirmation'];
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

    public function getName()
    {
        $connection = Connection::connect();
       $user = $connection
           ->createQueryBuilder()
            ->select('user_id', 'name')
            ->from('user_profiles')
            ->where('user_id = ?')
           ->setParameter(0,$_SESSION['user_id']);

        return new View("layout.html", [
            'currentUser'=>$user
        ]);
    }

}