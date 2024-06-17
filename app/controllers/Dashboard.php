<?php

use app\core\Controller;
use app\models\Auth;

class Dashboard extends Controller
{
    public function __construct()
    {
        if (!Auth::logged_in()) return $this->redirect('login');
    }
    public function index()
    {
        return $this->view('dashboard');
    }
}
