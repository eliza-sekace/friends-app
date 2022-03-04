<?php

namespace App\Controllers;

use App\Database\Connection;
use App\Redirect;
use App\Views\View;

class InvitesController
{
    public function index()
    {
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

        return new View("Users/invites.html",[
            'users'=>$query->fetchAllAssociative(),
        ]);
    }

    public function store($vars)
    {
        $connection = Connection::connect();
        $connection
            ->insert('friends', [
                'friends_id' => $_SESSION['user_id'],
                'user_id' => $vars['id'],
            ]);
        return new Redirect('/users/all');
    }

    public function accept($vars)
    {
        $connection = Connection::connect();
        $connection
            ->insert('friends', [
                'friends_id' => $_SESSION['user_id'],
                'user_id' => $vars['id'],
            ]);
        return new Redirect('/invites');
    }


}


