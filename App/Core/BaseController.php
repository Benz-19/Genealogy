<?php

namespace App\Core;


class BaseController
{
    public function renderView($view, $data = [])
    {
        extract($data);
        $path = dirname(__DIR__, 2) . "/resources/Views/$view.php";

        if (file_exists($path)) {
            require $path;
        } else {
            http_response_code(500);
            echo "View not found: $path";
        }
    }

/**
 * The ensureLoggedIn checks if a user is logged in
 * @param var $is_logged_in stores the logged in state obtained from a session
 * @return bool
 */
    public function ensureLoggedIn(): bool
    {
        $is_logged_in = $_SESSION['is_logged_in']; //gets the user logged in state

        if($is_logged_in !== 1 || empty($is_logged_in)){
            $_SESSION['error'] = "Something went wrong. You're not logged in. Access denied!!!";
            return false;
        }

        return true;
    }


/**
 * The ensureRole checks the user role
 * @param var $role stores the user type
 * @param var $user_role holds the required user type
 * @return bool
 */
    public function ensureRole($user_role): bool
    {
        $role = $_SESSION['user_details']['user_type'];

        if($role !== $user_role || empty($role)){
            $_SESSION['error'] = "Something went wrong. You don't have access in here. Access denied!!!";
            return false;           
        }

        return true;
    }
}
