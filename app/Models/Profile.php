<?php

namespace App\Models;

class Profile
{
    private int $id;
    private int $userId;
    private string $name;
    private string $surname;
    private string $birthday;

    public function __construct($id, $userId, $name, $surname, $birthday = '')
    {
        $this->id = $id;
        $this->userId = $userId;
        $this->name = $name;
        $this->surname = $surname;
        $this->birthday = $birthday;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getSurname()
    {
        return $this->surname;
    }

    public function getFullName()
    {
        return $this->name . ' ' . $this->surname;
    }

    public function getBirthday()
    {
        return $this->birthday;
    }

    public static function make($attributes)
    {
        if (!isset($attributes['id'])
            || !isset($attributes['user_id'])
            || !isset($attributes['name'])
            || !isset($attributes['surname'])
        ) {
            return null;
        }

        return new self($attributes['id'], $attributes['user_id'], $attributes['name'], $attributes['surname'], $attributes['birthday'] ?? '');
    }
}


