<?php
namespace App\Services\User;

use App\Models\Database\DB;
use App\Services\User\UserServiceInterface;
use Exception;

class UserService implements UserServiceInterface{
    private $db;

    public function __construct()
    {
        $this->db = new DB();
    }

    public function isUser(string $email): bool
    {
        $query = "SELECT id FROM genealogy_users WHERE email = :email LIMIT 1";
        $result = $this->db->fetchSingleData($query, [':email' => $email]);

        if($result !== null){
            return true;
        }else{
            return false;
        }
    }

    public function userId(string $email)
    {
        try{
            $query = "SELECT id FROM genealogy_users WHERE email=:e LIMIT 1";
            $params = [":e" => $email];

            $id = $this->db->fetchSingleData($query, $params);

            if(!empty($id) && $id !== null){
                return $id;
            }else{
                error_log("Error: Failed to get user id at UserService::userId");
                throw new Exception("User not found!");
                }
                
        }catch(Exception $error){
            error_log("Error: Failed to get user id at UserService::userId");
            throw $error;
        }
    }

    public function getTopPerformers(int $limit): array
    {
        throw new \Exception('Not implemented');
    }

    public function getUserById(int $id): ?object
    {
        throw new \Exception('Not implemented');
    }

    public function updateProfile(int $userId, array $data): bool
    {
        throw new \Exception('Not implemented');
    }
}