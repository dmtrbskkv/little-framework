<?php


namespace App\Controllers;


use App\Extensions\Request;
use App\Extensions\View;
use App\Models\User;

/**
 * Class UserController
 * @package App\Controllers
 */
class UserController extends Controller
{
    /**
     * Endpoints for methods with "header" function
     */
    const ENDPOINTS = [
        'success_login' => '/',
        'success_register' => '/',
        'success_logout' => '/'
    ];

    /**
     * Register new User by email and password
     */
    public function registerUser()
    {
        $request = new Request();

        $email = $request->input('email');
        $password = $request->input('password');
        $password_repeat = $request->input('password_repeat');


        // Check email
        if (!$email) {
            $this->showRegisterForm(['Please enter email']);
        }
        // Check password
        if(!$password){
            $this->showRegisterForm(['Please enter password']);
        }
        if (!$password || !$password_repeat || $password != $password_repeat) {
            $this->showRegisterForm(['Passwords do not match']);
        }

        // Check user exist
        if ($this->getUser($email)) {
            $this->showRegisterForm(['User already isset']);
        }

        // Save user to DB
        $user = new User();
        $user->email = $email;
        $user->password = md5($password);
        $user->rights = 20;

        // Check save process
        if (!$user->save()) {
            $this->showRegisterForm(['Something goes wrong']);
        }

        // Redirect to endpoint
        header("Location: " . self::ENDPOINTS['success_register']);
    }

    /**
     * Login user by email and password
     */
    public function loginUser()
    {
        $request = new Request();

        $email = $request->input('email');
        $password = $request->input('password');

        // Check email
        if (!$email) {
            $this->showLoginForm(['Please enter email']);
        }
        // Check password
        if (!$password) {
            $this->showLoginForm(['Please enter password']);
        }

        // Check user exist and then password
        $user = $this->getUser($email);
        $password = md5($password);
        if (!$user || $password !== $user['password']) {
            $this->showLoginForm(['Error on login']);
        }

        // Save login details to cookies
        if (!defined('APP_USERS_COOKIES_EMAIL')
            || !defined('APP_USERS_COOKIES_PASSWORD')) {
            $this->showLoginForm(['Error on login']);
        }
        setcookie(APP_USERS_COOKIES_EMAIL, $email);
        setcookie(APP_USERS_COOKIES_PASSWORD, $password);

        // Redirect to endpoint
        header("Location: " . self::ENDPOINTS['success_login']);
    }

    /**
     * Logout user
     */
    public function logoutUser()
    {
        setcookie(APP_USERS_COOKIES_EMAIL, null);
        setcookie(APP_USERS_COOKIES_PASSWORD, null);
        header("Location: " . self::ENDPOINTS['success_logout']);
    }

    /**
     * Get current user or user by "Where" condition.
     * When "Where" condition is string, check $where and user email
     *
     * Array "Where" condition example: [ ["'title' LIKE '%test%'", 'AND'], ...[] ]
     *
     * @param mixed $where email or array of "Where" condition
     * @return array|null
     */
    public function getUser($where = null): ?array
    {
        if (is_string($where)) {
            $where = [["email='{$where}'"]];
        }
        return (new User())
            ->where($where)
            ->first();
    }

    public function getCurrentUser(): ?array
    {
        $email = $_COOKIE['user_email'] ?? '';
        $password = $_COOKIE['user_password'] ?? '';
        $where = [["email='{$email}'", "AND"], ["password='{$password}'", "AND"]];
        return (new UserController())->getUser($where);
    }

    public function isAdmin(): bool
    {
        $user = View::getData('user') ? View::getData('user') : $this->getCurrentUser();
        $user_rights = $user['rights'] ?? false;
        return $user_rights >= 50;
    }

    public function isRoot(): bool
    {
        $user = View::getData('user') ? View::getData('user') : $this->getCurrentUser();
        $user_rights = $user['rights'] ?? false;
        return $user_rights >= 100;
    }

    /**
     * Get user from cookies
     * @return array|null
     */
    public function getCookiesUser(): ?array
    {
        $email = $_COOKIE[APP_USERS_COOKIES_EMAIL] ?? '';
        return (new UserController())->getUser($email);
    }

    /**
     * Show login form with messages or errors
     * @param array $errors
     * @param array $messages
     * @return View
     */
    public function showLoginForm(array $errors = []): View
    {
        $data['errors'] = $errors;
        return new View('login', $data);
    }

    /**
     * * Show register form with messages or errors
     * @param array $errors
     * @return View
     */
        public function showRegisterForm(array $errors = []): View
    {
        $data['errors'] = $errors;
        return new View('register', $data);
    }

}