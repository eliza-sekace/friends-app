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
            ->setParameter(0, $vars["id"])
            ->where('id = ?')
            ->executeQuery()
            ->fetchAssociative();

        $connection = Connection::connect();
        $connection
            ->insert('article_comments', [
                'user_id' => $_SESSION['user_id'],
                'article_id' => $result['id'],
                'text' => $_POST['comment']
            ]);

       $allComments = [];
       $allComments[] = $_POST['comment'];
        return new Redirect("/articles/{$vars["id"]}");
    }

    public function delete($vars)
    {

        $connection = Connection::connect();
        $result = $connection
            ->createQueryBuilder()
            ->select('id', 'user_id', 'article_id')
            ->from('article_comments')
            ->where('id = ?')
            ->setParameter(0, $vars['commentId'])
            ->executeQuery()
            ->fetchAssociative();

        if ($_SESSION['user_id'] == $result['user_id']) {
            $connection
                ->delete('article_comments', ['id' => (int)$vars['commentId']]);
        }
        return new Redirect("/articles/{$result['article_id']}");
    }
}

