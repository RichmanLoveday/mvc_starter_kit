<?php
/*
** User Model
*
*/

declare(strict_types=1);

namespace app\models;

use app\core\Model;
use DateTime;

class User extends Model
{

    protected $table = 'users';

    protected $allowedColumns = [
        'userID',
        'email',
        'firstName',
        'lastName',
        'password',
        'gender',
        'state',
        'phone',
        'city',
        'status',
        'photo',
        'status',
        'aboutMe',
        'yearsOfExperience',
        'cv',
        'date',
    ];

    protected $beforeInsert = [
        'user_id',
        'password_hash',
    ];

    protected $beforeUpdate = [
        'password_hash',
    ];

    protected $afterSelect = [];


    public function validate(array $data): bool
    {
        //? show($data); die;
        $this->errors = [];

        //? Validate full Name
        if (isset($data['firstName'])) {
            if (empty($data['firstName'])) {
                $this->errors['firstName'] = 'Pls fill in this field';
            } elseif (!preg_match('/^[a-zA-Z ]+$/', $data['firstName'])) {
                $this->errors['firstName'] = 'Only letters allowed in Full Name';
            }
        }

        if (isset($data['lastName'])) {
            if (empty($data['lastName'])) {
                $this->errors['lastName'] = 'Pls fill in this field';
            } elseif (!preg_match('/^[a-zA-Z ]+$/', $data['lastName'])) {
                $this->errors['lastName'] = 'Only letters allowed in Full Name';
            }
        }


        //? Validate Email 
        if (isset($data['email'])) {
            if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $this->errors['email'] = 'Pls fill in this field';
            } elseif (!empty($data['email'])) {

                // $this->where('email', $data['email']);

                $this->query('SELECT * FROM users WHERE email = :email');
                $this->bind(':email', $data['email']);
                $this->resultSet();

                if ($this->rowCount() > 0) {
                    $this->errors['email'] = 'Email already exists';
                }
            }
        }


        //? check for occupation
        if (isset($data['address'])) {
            if (empty($data['address'])) {
                $this->errors['address'] = "Please fill in this field";
            }
        }

        // check for city
        if (isset($data['city'])) {
            if (empty($data['city'])) {
                $this->errors['city'] = "Please fill in this field";
            }
        }

        // check for nationality
        if (isset($data['nationality'])) {
            if (empty($data['nationality'])) {
                $this->errors['nationality'] = "Please fill in this field";
            }
        }

        // check for city
        if (isset($data['state'])) {
            if (empty($data['state'])) {
                $this->errors['state'] = "Please fill in this field";
            }
        }

        // Validate phone number
        if (isset($data['phone'])) {
            if (empty($data['phone'])) {
                $this->errors['phone'] = "Please fill in this field";
            } elseif (preg_match('/[a-zA-Z]/', $data['phone'])) {
                $this->errors['phone'] = "Only numbers is allowed";
            } elseif (strlen($data['phone']) < 8) {
                $this->errors['phone'] = "Phone number is short";
            } elseif (strlen($data['phone']) > 12) {
                $this->errors['phone'] = "Phone number is too long";
            }
        }


        //? Validate Gender
        $gender = ['Male', 'Female'];

        if (isset($data['gender']) && empty($data['gender']) && !in_array($data['gender'], $gender)) {
            $this->errors['gender'] = 'Gender is not valid';
        }

        // Validate Password
        if (isset($data['password'])) {

            if (empty($data['password'])) {
                $this->errors['password'] = 'Pls fill in this field';
            } elseif (strlen($data['password']) < 6) {
                $this->errors['password'] = 'Password must be atleast 6 characters long ';
            } elseif ($data['password'] !== $data['confirm_password']) {
                $this->errors['password'] = 'The password do not match';
            } else {
                echo '';
            }
        }

        // Checking if errors are empty
        if (empty($this->errors)) {
            return true;
        }

        // show($this->errors); die;
        return false;
    }


    public function user_id(array $data)
    {
        $data['userID'] = random_string(30);

        while ($this->where('userID', $data['userID'])) {
            $data['userID'] .= rand(10, 1000);
        }

        return $data;
    }


    public function password_hash(array $data)
    {

        if (isset($data['password'])) {
            $data['password'] = hash('sha256', $data['password']);
        }
        return $data;
    }
}
