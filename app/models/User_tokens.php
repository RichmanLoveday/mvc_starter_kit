<?php

namespace app\models;
use app\core\Model;

class User_tokens extends Model {
    protected $table = 'user_tokens';

    protected $allowedColumns = [
        'userID',
        'selector',
        'hashed_validator',
        'expiry', 
    ];

    protected $beforeInsert = [];

    protected $afterSelect = [];


    //? store a new cookie to remember
    public function remember_me(string $user_id, int $day = 30)
    {
        [$selector, $validator, $token] = $this->generate_tokens();

        // remove all existing token associated with the user id
        $this->delete_user_token($user_id);

        // set expiration date
        $expired_seconds = time() + COOKIE_DURATION;

        // insert a token to the database
        $hashed_validator = password_hash($validator, PASSWORD_DEFAULT);
        $expiry = date('Y-m-d H:i:s', $expired_seconds);

        if($this->insert(['userID' => $user_id, 'selector' => $selector, 'hashed_validator' => $hashed_validator, 'expiry' => $expiry])) {
            setcookie('remember_me', $token, $expired_seconds);
        }
    }

    //? delete user token
    public function delete_user_token($user_id) {
        $userTokenRow = $this->where('userID', $user_id);
        if(!empty($userTokenRow)) {
            $this->delete($userTokenRow[0]->id);
        }
    }


    //? find user by token
    public function find_user_by_token(string $token): ?array
    {
        $tokens = $this->parse_token($token);

        if (!$tokens) {
            return null;
        }

        $this->query('SELECT * FROM user_tokens WHERE selector = :selector AND expiry >= NOW() LIMIT 1');
        $this->execute(['selector' => $tokens[0]]);

        if ($this->rowCount() > 0) {
            $result = $this->resultSet();
            return $result;
        }
    
        return [];
    }

    //? check for valid token
    public function token_is_valid(string $token): bool {

        [$selector, $validator] = $this->parse_token($token);

        $tokens = $this->find_user_token_by_selector($selector);
    

        if(!$tokens) return false;
        
        return password_verify($validator, $tokens[0]->hashed_validator);
    }
    
    
    //? find a user by selector
    public function find_user_token_by_selector(string $selector)
    {
        
        $this->query('SELECT * FROM user_tokens WHERE selector = :selector AND expiry >= NOW() LIMIT 1');
        $this->execute(['selector' => $selector]);
    
        if($this->rowCount() > 0) return $this->resultSet();

        return false;
    }


    //? get the token selector and validator parts 
    private function parse_token(string $token): ?array
    {
        $parts = explode(':', $token);
    
        if ($parts && count($parts) == 2) {
            return [$parts[0], $parts[1]];
        }
        return null;
    }


    //? generate a new token to be inserted and stored in cookie
    private function generate_tokens(): array
    {
        $selector = bin2hex(random_bytes(16));
        $validator = bin2hex(random_bytes(32));
    
        return [$selector, $validator, $selector . ':' . $validator];
    }
    
}