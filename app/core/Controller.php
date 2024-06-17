<?php
/*
** Master Controller
*
*/

declare(strict_types=1);

namespace app\core;

use app\models\Auth;
use app\models\ChatRoom;

class Controller
{
    public function view(string $view, array $data = []): void
    {
        extract($data);


        if (file_exists("../app/views/" . $view . ".php")) {
            require("../app/views/" . $view . ".php");
        } else {
            require("../app/views/404.php");
        }
    }

    public function load_model($model)
    {

        if (file_exists("../app/models/" . ucwords($model) . ".php")) {
            require("../app/models/" . ucwords($model) . ".php");
            return $model = new $model();
        }
        return false;
    }

    public function redirect($link)
    {
        header('Location: ' . URLROOT . trim($link, "/"));
        die();
    }

    public function controller_name()
    {
        return get_class($this);
    }


    public function getAllUnreadMessages(array $chatsMessages): int
    {
        $chats = 0;
        foreach ($chatsMessages as $chat) {
            if ($chat->unread > 0) {
                $chats = $chats + $chat->unread;
            }
        }
        return $chats;
    }



    protected function sendJsonResponse($status, $message, $data = null)
    {
        $response['status'] = $status;
        $response['message'] = $message;

        if ($data !== null) {
            $response['data'] = $data;
        }

        echo json_encode($response);
        exit;
    }
}
