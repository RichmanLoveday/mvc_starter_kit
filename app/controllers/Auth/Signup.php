<?php
/*
** Signup Controller
*
*/

use app\core\Controller;
use app\models\Auth;
use app\models\User;
use app\models\User_tokens;

class Signup extends Controller
{

    private $jsonData;

    public function __construct()
    {
        //? check if user is logged in
        if (Auth::logged_in()) return $this->redirect('dashboard');

        $this->jsonData = file_get_contents("php://input");
        $this->jsonData = json_decode($this->jsonData);
    }

    public function index()
    {
        $this->view('register', ['pageTitle' => 'Register']);
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $formData = (object) $_POST;

            $user = new User();
            $validated = $user->validate((array) $formData);

            if ($validated) {
                //? add to active status
                //$formData->status = USER_ACTIVE;
                $formData->date = now();

                $user->insert((array) $formData);

                $_SESSION['message'] = "Registered Successfully";
                $data['redirectUrl'] = URLROOT . 'dashboard';

                //? get user data from db
                $userData = $user->where('email', $formData->email)[0];

                //? check for remember me
                $userToken = new User_tokens();

                //? check if remember_me is added
                if ($formData->remember) {
                    $userToken->remember_me($userData->userID);
                }

                //? Authenticate user data
                Auth::authenticate($userData);

                return $this->sendJsonResponse(STATUS_SUCCESS, 'User registered successfully', $data);
            } else {
                //? handle error messages
                $data['errors'] = $user->errors;
                return $this->sendJsonResponse(STATUS_ERROR, 'Validation failed', $data);
            }
        } else {
            return $this->sendJsonResponse(STATUS_ERROR, 'Incorrect request sent');
        }
    }
}
