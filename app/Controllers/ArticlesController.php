<?php

namespace App\Controllers;

use App\Models\Article;
use App\Views\View;
use App\Database\Connection;


class ArticlesController
{
    public function index()
    {
        $connection = Connection::connect();

        $results = $connection
            ->createQueryBuilder()
            ->select('id', 'title', 'text')
            ->from('articles')
            ->executeQuery()
            ->fetchAllAssociative();

        $articles = [];
        foreach ($results as $result) {
            $articles[] = new Article($result['id'], $result['title'], $result['text']);
        }
        return new View("Articles/index.html", ['articles' => $articles]);
    }

    public function show($vars)
    {
        $connection = Connection::connect();

        $result = $connection
            ->createQueryBuilder()
            ->select('id', 'title', 'text')
            ->from('articles')
            ->where('id = ?')
            ->setParameter(0, $vars["id"])
            ->executeQuery()
            ->fetchAssociative();

        $article = new Article($result['id'], $result['title'], $result['text']);
        return new View("Articles/show.html", ["article" => $article]);
    }

    public function create()
    {
        return new View("Articles/create.html");
    }

    public function store($vars)
    {
        $connection = Connection::connect();

        $connection->createQueryBuilder()
            ->insert('articles')
            ->setValue('title', '?')
            ->setValue('text', '?')
            ->setParameter(0, $_POST['title'])
            ->setParameter(1, $_POST['text'])
            ->executeQuery();

        return $this->index();
    }

}