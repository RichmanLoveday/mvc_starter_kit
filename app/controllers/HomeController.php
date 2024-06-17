<?php

namespace App\Controllers;

use App\core\Controller;

class HomeController extends Controller
{
    public function index()
    {
        $this->view('home/index');
    }
}
