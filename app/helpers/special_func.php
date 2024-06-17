<?php

declare(strict_types=1);

use Gumlet\ImageResize;

function clean_url($url)
{
    $clean = rtrim($url, '/');
    $clean = filter_var($clean, FILTER_SANITIZE_URL);
    $clean = explode('/', $clean);

    return $clean;
}

function get_var(string $key, $default = NULL)
{
    if (isset($_POST[$key])) {
        return $_POST[$key];
    } elseif (isset($_GET[$key])) {
        return $_GET[$key];
    }
    return $default;
}


function clean_data($var)
{
    return htmlspecialchars(trim($var));
}

function get_select(string $key, string $value): string
{
    if (isset($_POST[$key])) {
        if ($_POST[$key] == $value) {
            return "selected";
        }
    }

    return "";
}


function random_string(int $lenght): string
{

    $array = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
    $text = '';
    for ($x = 0; $x < $lenght; $x++) {
        $random = rand(0, 61);
        $text .= $array[$random];
    }
    return $text;
}


function get_date($data)
{
    return date('M jS, Y', strtotime($data));
}

function get_date_time($data)
{
    return date('M jS, Y h:i:A', strtotime($data));
}

function make_date($data)
{
    return date('Y-m-d', strtotime($data));
}

function now()
{
    return date('Y-m-d H:i:s', time());
}

function midNightTime()
{
    return date('Y-m-d 00:00:00', strtotime('today'));
}


function currentYear()
{
    return date('Y');
}


function futureDate(string $currentTime, string|int $duration)
{
    return date('Y-m-d H:i:s', strtotime(
        "+$duration months",
        strtotime($currentTime)
    ));
}

function futureDateInDays(string $currentTime, string|int $days)
{
    return date('Y-m-d H:i:s', strtotime(
        "+$days days",
        strtotime($currentTime)
    ));
}


function get_image($image, $gender = "male")
{
    // echo $image; die;

    if ($image == NULL) {
        $image = ASSETS . '/images/avatar/male_avatar.png';
        if ($gender == 'male') {
            $image = ASSETS . '/images/avatar/male_avatar.png';
        } else {
            $image = ASSETS . '/images/avatar/female_avatar.png';
        }
    }

    return $image;
}

function view_path(string $view): string
{
    //extract($data);

    if (file_exists("../app/views/" . $view . ".inc.php")) {
        return "../app/views/" . $view . ".inc.php";
    } else {
        return "../app/views/404.view.php";
    }
}


function show($data)
{
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}


function upload_image(array $file, int $width, int $height, string $dirName)
{
    //check for files
    if (count($file) > 0) {
        // file exist
        $allowed[] = 'image/jpeg';
        $allowed[] = 'image/jpg';
        $allowed[] = 'image/png';

        // check if type of file is in array
        if ($file['image']['error'] == 0 && in_array($file['image']['type'], $allowed)) {

            // create a folder to move files
            if (!file_exists("uploads/$dirName/") && !is_dir("uploads/$dirName/")) {
                mkdir("uploads/$dirName/");
            }

            $folder = "uploads/$dirName/";
            $filename = $file['image']['name'];

            // resize image 
            $destination = $folder . time() . '.' . strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $resizer = new ImageResize($file['image']['tmp_name']);
            $resizer->resize($width, $height);
            $resizer->save($destination);

            return $destination;
        }

        return false;
    }
    return false;
}


function unlinkImage($dir)
{
    // remove existing image from directory
    if (file_exists($dir)) {
        @unlink($dir);
    }
}


function include_path($path)
{
    return include("includes/scripts.php");
}
