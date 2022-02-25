<?php

namespace App\Controllers;


use App\Views\UsersView;

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
}