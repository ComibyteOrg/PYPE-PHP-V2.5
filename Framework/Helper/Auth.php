<?php
namespace Framework\Helper;

use Framework\Database\DatabaseQuery;

class Auth
{
    private $userData = null;
    private $db;

    public function __construct()
    {
        $this->db = new DatabaseQuery();
    }

    public function check()
    {
        return Helper::check();
    }

    public function id()
    {
        $a = Helper::auth();
        // If Helper::auth() returns a full user record (array or object), return its id
        if (is_array($a)) {
            return $a['id'] ?? null;
        }

        if (is_object($a)) {
            return $a->id ?? null;
        }

        // Otherwise it's likely an id or null
        return $a;
    }

    public function user()
    {
        return $this->getUserData();
    }

    private function getUserData()
    {
        if ($this->userData !== null) {
            return $this->userData;
        }
        // If Helper::auth() already returned a full user record, use it
        $a = Helper::auth();
        if (is_array($a) || is_object($a)) {
            $this->userData = is_object($a) ? (array) $a : $a;
            return $this->userData;
        }

        $id = $this->id();
        if (!$id) {
            return null;
        }

        try {
            // Fetch user from 'users' table using DatabaseQuery fallback
            $result = $this->db->select('users', '*', "id = ?", "i", [$id]);

            if ($result && $result->num_rows > 0) {
                $this->userData = $result->fetch_assoc();
                return $this->userData;
            }
        } catch (\Exception $e) {
            // If table doesn't exist or other error, show message as requested
            die("Error: users table does not exist or database error: " . $e->getMessage());
        }

        return null;
    }

    public function __get($name)
    {
        $data = $this->getUserData();
        if ($data && isset($data[$name])) {
            return $data[$name];
        }
        return null;
    }

    public function __isset($name)
    {
        $data = $this->getUserData();
        return isset($data[$name]);
    }
}
