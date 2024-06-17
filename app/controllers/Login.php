<?php
/*
** Login Controller
*
*/

declare(strict_types=1);

use app\core\Controller;
use app\models\Auth;
use app\models\User;
use app\models\User_tokens;

class Login extends Controller
{
    protected $jsonData;
    public function __construct()
    {
        //? check if user is logged in
        if (Auth::logged_in()) return $this->redirect('dashboard');

        $this->jsonData = file_get_contents("php://input");
        $this->jsonData = json_decode($this->jsonData);
    }

    public function index()
    {
        if ($this->jsonData) {
            $email = $this->jsonData->email;
            $password = $this->jsonData->password;

            $testEmail = 'testing@test.com';
            $testPassword = '12345';

            if ($email == $testEmail && $password == $testPassword) {

                //? Authenticate user data
                Auth::authenticate((object) [
                    'name' => 'Test Name',
                    'email' => $email,
                    'password' => $password
                ]);

                $_SESSION['message'] = "Logged In successfully";
                $data['redirectUrl'] = URLROOT . 'dashboard';

                return $this->sendJsonResponse(
                    STATUS_SUCCESS,
                    'Login Successfully',
                    $data
                );
            } else {

                return $this->sendJsonResponse(
                    STATUS_ERROR,
                    'Email / Password Incorrect',
                );
            }
        }
        return $this->view('login-v2');
    }
}
