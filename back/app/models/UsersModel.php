<?php

namespace App\models;

use PDO;

class UsersModel {

    // get PDO from DI container
    function __construct(private PDO $db) {
    }

    /**
     * Create new user
     * @param array $body fields of form
     */
    function createUser(array $body) {
        $query = "INSERT INTO users
                    (username, email, password, birthdate, phone, url)
                    VALUES (:username, :email, :password, :birthdate, :phone, :url)";
        $stmt = $this->db->prepare($query);
        $params = [
            ':username' => $body['username'],
            ':email' => $body['email'],
            ':password' => $body['password'],
            ':birthdate' => $body['birthdate'],
            ':phone' => $body['phone'],
            ':url' => $body['url']
        ];
        return $stmt->execute($params);
    }

    /**
     * Delete a user
     * @param string $username
     * @return bool
     */
    function deleteUser(string $username): bool {
        $query = "DELETE FROM users WHERE username=:username";
        $stmt = $this->db->prepare($query);
        $params = [
            ':username' => $username
        ];
        $stmt->execute($params);
        return $stmt->rowCount() > 0;
    }

    /**
     * List of all users
     * @return array list of users
     */
    function usersList(): array {
        $query = "SELECT username, email FROM users";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get user by username
     * @param string $username
     * @return array users data
     */
    function getUser(string $username): array {
        $query = "SELECT * FROM users WHERE username=:username";
        $stmt = $this->db->prepare($query);
        $params = [
            ':username' => $username
        ];
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
