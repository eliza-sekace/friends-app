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

class CommentsController
{
    public function __construct()
    {
        //session_start();
        if (!isset($_SESSION['user_id'])) {
            header("location: /login", true);
        }
    }

    public function comment($vars)
    {
        $connection = Connection::connect();

        $result = $connection
            ->createQueryBuilder()
            ->select('id', 'user_id', 'title', 'text', 'created_at')
            ->from('articles')
            ->where('id = ?')
            ->setParameter(0, $vars["id"])
            ->executeQuery()
            ->fetchAssociative();


        $connection = Connection::connect();
        $connection
            ->insert('article_comments', [
                'user_id' => $_SESSION['user_id'],
                'article_id' => $result['id'],
                'text' => $_POST['comment']
            ]);
        var_dump($_GET);
        $allComments = [];
        $allComments[] = $_POST['comment'];
        return new Redirect("/articles/{$vars['id']}");

    }

//    public function show($vars)
//    {
//        $connection = Connection::connect();
//        $result = $connection
//            ->createQueryBuilder()
//            ->select('id', 'user_id', 'article_id', 'title', 'created_at')
//            ->from('article_comments')
//            ->where('id = ?')
//            ->setParameter(0, $vars["id"])
//            ->executeQuery()
//            ->fetchAssociative();
//
//        $comments = new Comment(
//            $result['id'],
//            $result['user_id'],
//            $result['article_id'],
//            $result['text'],
//            $result['created_at']);
//
//        return new View("Articles/show.html", [
//            'userId' => $result['user_id'],
//            'article_id' => $result['article_id'],
//            'text' => $result['text'],
//
//        ]);
//
//    }
//
//
}

