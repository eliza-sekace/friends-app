<?php

namespace App\Models;

class Article
{
    private int $id;
    private string $title;
    private string $text;

    public function __construct($id, $title, $text)
    {
        $this->id = $id;
        $this->title = $title;
        $this->text = $text;
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

    public function toArray()
    {
        return [
            "title" => $this->title,
            "text" => $this->text
        ];
    }


}


