<?php

namespace App\Controllers;

use App\Core\Body;
use App\Models\UsersModel;
use App\Core\Response;
use App\core\Validations;
use Exception;

/**
 * Handle users
 */
class UsersController {

    // list of available forms with filrs rules
    private array $forms = [
        'regForm' => [
            'username'      => ['type' => 'text', 'validations' => ['required', 'alphaOnly', ['lengthMax', 100]]],
            'email'         => ['type' => 'email', 'validations' => ['required', 'email', ['lengthMax', 100]]],
            'password'      => ['type' => 'password', 'validations' => ['required', ['lowercase', 1], ['uppercase', 1], ['special', 1], ['lengthMin', 8], ['lengthMax', 100]]],
            'birthdate'     => ['type' => 'date', 'validations' => ['required', 'date']],
            'phone'         => ['type' => 'tel', 'validations' => ['required', 'numericOnly', ['length', 10]]],
            'url'           => ['type' => 'url', 'validations' => ['required', 'url', ['lengthMax', 200]]],
        ]
    ];

    // get all from DIContainer
    function __construct(private UsersModel $userModel, private Body $body, private Validations $validations) {
    }

    /**
     * Get registration form fields list and rules
     * @return Response fields list
     */
    public function getRegForm(): Response {
        return new Response(200, 'success', $this->forms['regForm']);
    }

    /**
     * Add new user
     * @return Response error|success
     */
    public function addUser(): Response {
        if (!$this->validations->validate($this->body->data, $this->forms['regForm'])) {
            return new Response(200, 'error', 'validation failed');
        }

        try {
            $this->userModel->createUser($this->body->data);
            return new Response(200, 'success', 'ok');
        } catch (Exception $e) {
            return new Response(401, 'error', $e->getMessage());
        }
    }

    /**
     * Get all users
     * @return Response error|list
     */
    public function usersList(): Response {
        try {
            $users = $this->userModel->usersList();
            return new Response(200, 'success', $users);
        } catch (Exception $e) {
            return new Response(401, 'error', $e->getMessage());
        }
    }

    /**
     * Delete user
     * @param string $username
     * @return Response error|success
     */
    public function deleteUser(string $username): Response {
        try {
            if($this->userModel->deleteUser($username)) {
                return new Response(200, 'success', 'ok');
            } else {
                return new Response(200, 'error', 'not deleted');
            }
        } catch (Exception $e) {
            return new Response(401, 'error', $e->getMessage());
        }
    }

    /**
     * Get users' data
     * @param string $username
     * @return Response error|data
     */
    public function getUser(string $username): Response {
        try {
            $user = $this->userModel->getUser($username);
            return new Response(200, 'success', $user);
        } catch (Exception $e) {
            return new Response(401, 'error', $e->getMessage());
        }
    }
}
