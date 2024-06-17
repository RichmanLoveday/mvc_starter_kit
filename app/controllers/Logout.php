<?php
/*
** Logout Controller
*
*/

use app\core\Controller;
use app\models\Auth;

class Logout extends Controller
{

    public function __construct()
    {
        Auth::logout();
        $this->redirect('login');
    }
}
