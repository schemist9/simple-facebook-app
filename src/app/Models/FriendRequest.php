<?php

namespace App\Models;
use App\Models\CommentValidator;

class FriendRequest
{
    private array $errors = [];
    public ?int $id = null;
    public ?int $fromId = null;
    public ?int $toId = null;

    public function __construct(array $data)
    {
        $this->fromId = $data['from_id'];
        $this->toId = $data['to_id'];
    }

    public function create()
    {
        $pdo = \App\DB::get();
        $query = "INSERT INTO friend_requests (from_id, to_id) VALUES (:from, :to)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':from' => $this->fromId,
            ':to' => $this->toId
        ]);
        return $stmt;
    }

    public static function destroy(int $id)
    {
        $pdo = \App\DB::get();
        $query = "DELETE FROM friend_requests WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':id' => $id
        ]);
        return $stmt;
    }

    public static function findByFromAndToId(int $fromId, int $toId)
    {
        $pdo = \App\DB::get();
        $query = "SELECT * FROM friend_requests WHERE from_id = :from_id AND to_id = :to_id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([ ':from_id' => $fromId, ':to_id' => $toId ]);
        return $stmt->fetch()['id'] ?? null;
    }

    public static function findTo(int $toId)
    {
        $pdo = \App\DB::get();
        $query = "SELECT users.firstname AS requester_firstname, users.surname AS requester_surname, users.avatar AS requester_avatar, users.id AS requester_id FROM friend_requests 
            INNER JOIN users
                ON users.id = friend_requests.from_id 
            WHERE to_id = :to_id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([ ':to_id' => $toId ]);
        return $stmt->fetchAll();
    }


    public static function findByFrom(int $fromId)
    {
        $pdo = \App\DB::get();
        $query = "SELECT * FROM friend_requests WHERE from_id = :from_id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([ ':from_id' => $fromId ]);
        return $stmt->fetch();
    }
}