<?php

namespace App\Models;

class Comment
{
    private int $id;
    private int $userId;
    private string $articleId;
    private string $text;
    private string $createdAt;
    private array $all = [];

    public function __construct($id, $userId, $articleId, $text, $createdAt, $all = [])
    {
        $this->id = $id;
        $this->userId = $userId;
        $this->articleId = $articleId;
        $this->text = $text;
        $this->createdAt = $createdAt;
        $this->all = $all;
}

    public function getId()
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getArticleId(): string
    {
        return $this->articleId;
    }

    public function getText()
    {
        return $this->text;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

public function getAll()
{
    foreach ($this->all as $all){
        return $all;
    }
}

}


