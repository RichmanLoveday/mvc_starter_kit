<?php
/*
** Autentications models
*
*/

namespace app\models;

use DateTime;

class Auth
{

    // As row as an ID to a specific user
    public static function authenticate($row): bool
    {
        // Creating a session for every user logged in
        $_SESSION['USER'] = $row;
        return true;
    }


    // Logging out a user
    public static function logout()
    {
        // $userToken = new User_tokens();
        $user = new User();

        //? remove all existing token associated with the user id
        //  $userToken->delete_user_token(Auth::user()->userID ?? '');


        //?last seen
        $date = new DateTime();

        //? set online status
        // $user->update(self::user()->id, [
        //     'loginStatus' => 'offline',
        //     'lastSeen' => $date->format('Y-m-d H:i:s'),
        //     'connectionID' => NULL,
        //     'token' => NULL,
        // ]);

        // //? logging out a user and unseting a user logged in
        // if (isset($_SESSION['USER'])) {
        //     unset($_SESSION['USER']);
        // }


        // //? remove the remember_me cookie
        // if (isset($_COOKIE['remember_me'])) {
        //     unset($_COOKIE['remember_me']);
        //     setcookie('remember_me', null, -1);
        //     setcookie('return', null, -1);
        // }

        //? destroy all sessions
        session_destroy();
    }

    //? Checking if logged in
    public static function logged_in(): bool
    {
        // $userToken = new User_tokens();
        $userDetails = new User();

        //? checking if user is logged in
        if (isset($_SESSION['USER'])) {
            return true;
        }

        //? check the remember_me in cookie
        $token = filter_input(INPUT_COOKIE, 'remember_me', FILTER_SANITIZE_SPECIAL_CHARS);
        // if ($token && $userToken->token_is_valid($token)) {
        //     $user = $userToken->find_user_by_token($token)[0];

        //     if ($user) {
        //         //? get the user details details alone
        //         $user = $userDetails->where('userID', $user->userID)[0];

        //         //? check if user is banned
        //         if ($user->status == "banned") return false;

        //         return Auth::authenticate($user);
        //     }
        // }

        return false;
    }


    public static function user()
    {
        // Display user name
        if (isset($_SESSION['USER'])) {
            return $_SESSION['USER'];
        }
        return false;
    }



    // Calling a an unknown static method an performing a specific functionalities.
    public static function __callStatic($method, $params)
    {
        $prop = strtolower(str_replace('get', "", $method));

        if (isset($_SESSION['USER']->$prop)) {
            //show($_SESSION['USER']); die;
            return $_SESSION['USER']->$prop;
        }

        return 'Unknown';
    }

    // Access to different functionalities
    public static function access(string $rank = 'student'): bool
    {

        // 
        if (!isset($_SESSION['USER'])) {
            return false;
        }

        // Checking if rank is logged in
        $loged_in_rank = $_SESSION['USER']->rank;

        // creating who have access
        $RANK['super_admin']    = ['super_admin', 'lecturer', 'admin', 'reception', 'student'];
        $RANK['admin']          = ['admin', 'lecturer', 'reception', 'student'];
        $RANK['lecturer']       = ['lecturer', 'reception', 'student'];
        $RANK['reception']      = ['reception', 'student'];
        $RANK['student']        = ['student'];

        // if the login user in not set
        if (!isset($RANK[$loged_in_rank])) {
            return false;
        }

        // checking if select rank is in array
        if (in_array($rank, $RANK[$loged_in_rank])) {
            return true;
        }
        return false;
    }
}
