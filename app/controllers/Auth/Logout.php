<?php
/*
** Logout Controller
*
*/

declare(strict_types=1);

namespace app\controllers\auth;


use app\core\Controller;
use app\models\Auth;

class Logout extends Controller
{

    public function __construct()
    {
        Auth::logout();
        $this->redirect('auth/login');
    }
}
