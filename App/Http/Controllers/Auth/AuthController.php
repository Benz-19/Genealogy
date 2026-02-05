<?php
namespace App\Http\Controllers\Auth;

use App\Services\Auth\AuthService;
use App\Core\BaseController;
use App\Services\User\UserService;
use Exception;

class AuthController extends BaseController{
    private $auth_service;
    private $user_service;

    public function __construct()
    {
        $this->auth_service = new AuthService();
        $this->user_service = new UserService();
    }

    public function showLoginPage(){
        $this->renderView('Auth/login');
    }

    public function showRegisterPage(){
        $this->renderView('Auth/register');
    }


    public function register(){
        // Ensure the right request is received
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->renderView('Auth/register');
                return;
        }

        try {
            if(empty($_POST['email']) || empty($_POST['username']) || empty($_POST['password'])){
                $this->renderView("Auth\register", [
                    "errorMessage" => "Ensure all fields are filled."
                ]);
            }

            // Register a new user
            $data = [
                'username' => $_POST['username'],
                'email' => $_POST['email'],
                'password' => $_POST['password']
            ];

            $referral_code = $_POST['referral_code'];
            $success = $this->auth_service->register($data, $referral_code);

            if($success){
                $this->renderView("Auth/register", [
                    "errorMessage" => "Account successfully created."
                ]);                
            }else{

                // Redirect if with error message
                $this->renderView("Auth/register", [
                    "errorMessage" => "Something went wrong. Try again."
                ]);
            }            

        } catch (\Exception $error) {
            $this->renderView(
                "Auth/register",
                [
                    "errorMessage" => $error->getMessage(),
                    "oldInput" => $_POST
                ]
            );
        }
    }

    public function login(){
        // Ensure the right request is received
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->renderView('Auth/login');
                return;
        }

        try {
                // Login user
                $success = $this->auth_service->login($_POST['email'] ?? '', $_POST['password'] ?? '');
                if($success === true){
                    $_SESSION['is_logged_in'] = true;
                    $_SESSION['user_id'] = $this->user_service->userId($_POST['email'])['id'];
                    header("Location: /genealogy-dashboard?success=1");
                    exit; 
                }else{
                    $this->renderView('Auth/login', [
                    'errorMessage' => "User does not exists",
                    'oldInput' => $_POST
                    ]);
                }

            } catch (Exception $e) {
                // use renderView to keep data and show the error
                $this->renderView('Auth/register', [
                    'errorMessage' => $e->getMessage(),
                    'oldInput' => $_POST
                ]);
            }
    }
}