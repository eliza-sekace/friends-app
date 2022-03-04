<?php

namespace App\Controllers;

use App\Exceptions\FormValidationException;
use App\Exceptions\ResourceNotFoundException;
use App\Models\Article;
use App\Models\Comment;
use App\Repositories\ProfilesRepository;
use App\Validation\ArticleFormValidator;
use App\Validation\Errors;
use App\Views\View;
use App\Database\Connection;
use App\Redirect;


class ArticlesController
{
    public function __construct()
    {
        //session_start();
        if (!isset($_SESSION['user_id'])) {
            header("location: /login", true);
        }
    }

    public function index()
    {
        $connection = Connection::connect();

        $results = $connection
            ->createQueryBuilder()
            ->select('id', 'user_id', 'title', 'text', 'created_at')
            ->from('articles')
            ->orderBy('id', "desc")
            ->executeQuery()
            ->fetchAllAssociative();

        $articles = [];
        foreach ($results as $result) {
            $articles[] = new Article($result['id'], $result['user_id'], $result['title'], $result['text'], $result['created_at']);
        }
        return new View("Articles/index.html", [
            'articles' => $articles,
            'currentUser' => $_SESSION['user_id']
        ]);
    }

    public function show($vars)
    {
        try {
            $connection = Connection::connect();

            $result = $connection
                ->createQueryBuilder()
                ->select('id', 'user_id', 'title', 'text', 'created_at')
                ->from('articles')
                ->where('id = ?')
                ->setParameter(0, $vars["id"])
                ->executeQuery()
                ->fetchAssociative();

            if (!$result) {
                throw new ResourceNotFoundException("Article with id {$vars['id']} not found");
            }

            $article = new Article(
                $result['id'],
                $result['user_id'],
                $result['title'],
                $result['text'],
                $result['created_at']);

            $profileRepository = new ProfilesRepository();
            $profile = $profileRepository->getByUserId($result['user_id']);
            $currentUser = $_SESSION['user_id'];

            // make select query for article likes

            $articleLikes = $connection
                ->createQueryBuilder()
                ->select('COUNT(id)')
                ->from('likes')
                ->where('article_id =?')
                ->setParameter(0, (int)$vars['id'])
                ->executeQuery()
                ->fetchOne();


            $articleUserId = $connection
                ->createQueryBuilder()
                ->select('user_id')
                ->from('likes')
                ->where("user_id = {$currentUser} ")
                ->setParameter(0, (int)$vars['id'])
                ->andWhere('article_id =?')
                ->executeQuery()
                ->fetchOne();

        $comments= $connection
            ->createQueryBuilder()
            ->select('c.id', 'c.user_id', 'c.article_id', 'c.text', 'c.created_at', 'p.name', 'p.surname')
            ->from('article_comments', 'c')
            ->leftJoin('c', 'user_profiles','p', 'c.user_id=p.user_id')
            ->where('c.article_id = ?')
            ->setParameter(0, $vars["id"])
            ->orderBy('c.id', 'desc')
            ->executeQuery()
            ->fetchAllAssociative();


            //select count(id) from likes where article_id = $vars['id'}
            return new View("Articles/show.html", [
                'article' => $article,
                'profile' => $profile,
                'currentUser' => $currentUser,
                'articleLikes' => (int)$articleLikes,
                'userLike' => $articleUserId,
                'author' => $result['user_id'],
                'comments' => $comments,
            ]);
        } catch (ResourceNotFoundException $e) {
            var_dump($e->getMessage());
            return new View('404.html');
        }


    }

    public function create()
    {
        return new View("Articles/create.html", [
            'errors' => Errors::getAll(),
            'inputs' => $_SESSION['inputs'] ?? []
        ]);
    }

    public function store($vars): Redirect
    {
        try {
            $validator = new ArticleFormValidator($_POST, [
                'title' => ['required', "min:3"],
                'text' => ['required']
            ]);
            $validator->passes();
        } catch (FormValidationException $exception) {

            $_SESSION['errors'] = $validator->getErrors();
            $_SESSION['inputs'] = $_POST;

            return new Redirect('/articles/create');
        }

        $connection = Connection::connect();

        $connection
            ->insert('articles', [
                'user_id' => $_SESSION['user_id'],
                'title' => $_POST['title'],
                'text' => $_POST['text'],
            ]);


        return new Redirect('/articles');
    }

    public
    function delete(array $vars)
    {
        $connection = Connection::connect();

        $result = $connection
            ->createQueryBuilder()
            ->select('user_id')
            ->from('articles')
            ->where('id = ?')
            ->setParameter(0, $vars["id"])
            ->executeQuery()
            ->fetchAssociative();

        if ($_SESSION['user_id'] == $result['user_id']) {
            $connection
                ->delete('articles', ['id' => (int)$vars['id']]);
        }
        return new Redirect('/articles');
        //header("location: /articles", true);
    }

    public
    function edit($vars): View
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


        $article = new Article(
            $result['id'],
            $result['user_id'],
            $result['title'],
            $result['text'],
            $result['created_at']);

        return new View("Articles/edit.html", [
            "article" => $article
        ]);

    }

    public
    function update(array $vars)
    {
        $connection = Connection::connect();

        $result = $connection
            ->createQueryBuilder()
            ->select('id', 'user_id', 'title', 'text')
            ->from('articles')
            ->where('id = ?')
            ->setParameter(0, $vars["id"])
            ->executeQuery()
            ->fetchAssociative();

        if ($_SESSION['user_id'] == $result['user_id']) {
            $connection
                ->update('articles', [
                    'title' => $_POST['title'],
                    'text' => $_POST['text']
                ], ['id' => (int)$vars['id']]);
        }
        header("location: /articles/{$vars['id']}", true);
    }

    public
    function like(array $vars): Redirect
    {
        //make select query, check if user already liked.
        $articleId = (int)$vars['id'];
        $connection = Connection::connect();
        $connection->insert('likes', [
            'article_id' => $articleId,
            'user_id' => $_SESSION['user_id'] //ielikt session user id
        ]);
        return new Redirect("/articles/{$articleId}");
    }

}

