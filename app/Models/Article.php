<?php

namespace App\Models;

class Article
{
    private int $id;
    private int $userId;
    private string $title;
    private string $text;
    private string $createdAt;


    public function __construct($id,$userId, $title, $text, $createdAt)
    {
        $this->id = $id;
        $this->userId = $userId;
        $this->title = $title;
        $this->text = $text;
        $this->createdAt=$createdAt;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }
    public function getText()
    {
        return $this->text;
    }
    public function getCreatedAt()
    {
        return $this->createdAt;
    }


    public function toArray()
    {
        return [
            "title" => $this->title,
            "text" => $this->text
        ];
    }

    public function getUserId(): int
    {
        return $this->userId;
    }


}


