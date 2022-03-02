<?php

namespace App\Repositories;

use App\Database\Connection;
use App\Models\Profile;
use App\Models\User;

class ProfilesRepository
{
    private \Doctrine\DBAL\Connection $connection;

    public function __construct()
    {
        $this->connection = Connection::connect();
    }

    public function getByUserId(int $userId): ?Profile
    {
        $result = $this->connection
            ->createQueryBuilder()
            ->select('id', 'user_id', 'name', 'surname', 'birthday')
            ->from('user_profiles')
            ->where('user_id = ?')
            ->setParameter(0, $userId)
            ->executeQuery()
            ->fetchAssociative();

        return Profile::make($result);
    }
}