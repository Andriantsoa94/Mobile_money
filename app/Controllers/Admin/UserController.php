<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;

class UserController extends BaseController
{
    private UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index(): string
    {
        return $this->render('admin/users', [
            'title' => 'Utilisateurs',
            'users' => $this->userModel->orderBy('id', 'ASC')->findAll(),
        ]);
    }
}
