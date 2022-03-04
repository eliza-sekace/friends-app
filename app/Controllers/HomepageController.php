<?php

namespace App\Controllers;
;

use App\Models\Article;
use App\Models\Comment;
use App\Repositories\ProfilesRepository;
use App\Views\View;
use App\Database\Connection;
use Doctrine\DBAL\Exception\InvalidFieldNameException;
use App\Redirect;

class HomepageController
{
    public function index()
    {
        return new View("homepage.html", [ ]);
    }
}