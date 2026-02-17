<?php

use Framework\Helper\Helper;
use Framework\Helper\CSRF;

if (!function_exists('sanitize')) {
    function sanitize($input)
    {
        return Helper::sanitize($input);
    }
}

if (!function_exists('redirect')) {
    function redirect($page, $seconds = 0)
    {
        return Helper::redirect($page, $seconds);
    }
}

if (!function_exists('set_alert')) {
    function set_alert($type, $message)
    {
        return Helper::set_alert($type, $message);
    }
}

if (!function_exists('writetxt')) {
    function writetxt($file_name, $values = array())
    {
        return Helper::writetxt($file_name, $values);
    }
}

if (!function_exists('deletetxt')) {
    function deletetxt($file_name, $cond)
    {
        return Helper::deletetxt($file_name, $cond);
    }
}

if (!function_exists('returnJson')) {
    function returnJson(array $data, int $statusCode = 200)
    {
        Helper::returnJson($data, $statusCode);
    }
}

if (!function_exists('excerpt')) {
    function excerpt(string $html, int $length = 150, string $suffix = '...')
    {
        return Helper::excerpt($html, $length, $suffix);
    }
}

if (!function_exists('readingTime')) {
    function readingTime(string $content, int $wpm = 200)
    {
        return Helper::readingTime($content, $wpm);
    }
}

if (!function_exists('dd')) {
    function dd(...$args)
    {
        Helper::dd(...$args);
    }
}

if (!function_exists('env')) {
    function env(string $key, $default = null)
    {
        return Helper::env($key, $default);
    }
}

if (!function_exists('asset')) {
    function asset(string $path)
    {
        return Helper::asset($path);
    }
}

if (!function_exists('url')) {
    function url(string $path, array $parameters = [])
    {
        return Helper::url($path, $parameters);
    }
}

if (!function_exists('slugify')) {
    function slugify(string $string, string $separator = '-')
    {
        return Helper::slugify($string, $separator);
    }
}

if (!function_exists('session')) {
    function session(string $key, $default = null)
    {
        return Helper::session($key, $default);
    }
}

if (!function_exists('old')) {
    function old(string $key)
    {
        return Helper::old($key);
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field()
    {
        return CSRF::getTokenField();
    }
}

if (!function_exists('csrfField')) {
    function csrfField()
    {
        return CSRF::getTokenField();
    }
}

if (!function_exists('csrfInput')) {
    function csrfInput()
    {
        return CSRF::getTokenField();
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token()
    {
        return CSRF::generateToken();
    }
}

if (!function_exists('csrf_verify')) {
    function csrf_verify(string $token)
    {
        return CSRF::validateToken($token);
    }
}

if (!function_exists('csrf_enforce')) {
    /**
     * Enforce CSRF for form submissions.
     * If token missing or invalid, render a 419 Page Expired view and exit.
     */
    function csrf_enforce()
    {
        $tokenName = CSRF::getTokenName();
        $submitted = $_POST[$tokenName] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $_SERVER['X-CSRF-TOKEN'] ?? '';

        if (empty($submitted) || !CSRF::validateToken($submitted)) {
            http_response_code(419);

            // Check if expectation is JSON
            if (
                (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ||
                (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'json') !== false)
            ) {

                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'CSRF token missing or invalid', 'error' => '419 Page Expired']);
                exit;
            }

            // Simple plain response (no template) — consistent with router errorResponse
            echo "<div style='font-family: Arial; text-align: center; display: flex; height: 100vh; align-items: center; justify-content: center;'>"
                . "<div><h2>419</h2><p>Page Expired — the form token is missing or invalid. Please reload and try again.</p></div>"
                . "</div>";
            exit;
        }
    }
}

if (!function_exists('getCSRFName')) {
    function getCSRFName()
    {
        $tokenName = CSRF::getTokenName();
        return $_POST[$tokenName] ?? '';
    }
}

if (!function_exists('view')) {
    function view(string $viewName, array $data = [], bool $return = false)
    {
        // Check if we should use Twig (based on file extension or automatic detection)
        $useTwig = false;
        $templateName = $viewName;

        // If view name already has twig extension, use Twig
        if (strpos($viewName, '.twig') !== false) {
            $useTwig = true;
            $templateName = str_replace('.', '/', $viewName);
        } else {
            // Check if a Twig template exists for this view
            $viewPath = \Framework\Router\Route::getViewPath();
            $twigTemplate = $viewPath . '/' . str_replace('.', '/', $viewName) . '.twig';

            if (file_exists($twigTemplate)) {
                $useTwig = true;
                $templateName = str_replace('.', '/', $viewName) . '.twig';
            }
        }

        if ($useTwig) {
            // Use Twig template
            try {
                $output = \Framework\Helper\TwigManager::render($templateName, $data);
                if ($return)
                    return $output;
                echo $output;
                return;
            } catch (\Exception $e) {
                $msg = "<b>Error:</b> Twig template error: " . $e->getMessage();
                if ($return)
                    return $msg;
                echo $msg;
                return;
            }
        } else {
            // Use traditional PHP templates
            $viewPath = \Framework\Router\Route::getViewPath();

            if (empty($viewPath)) {
                $msg = "<b>Error:</b> View path not configured.";
                if ($return)
                    return $msg;
                echo $msg;
                return;
            }

            $file = $viewPath . '/' . str_replace('.', '/', $viewName) . '.php';

            if (file_exists($file)) {
                extract($data);
                ob_start();
                require $file;
                $output = ob_get_clean();
                if ($return)
                    return $output;
                echo $output;
                return;
            } else {
                $msg = "<b>Error:</b> View '{$viewName}' not found at {$file}.";
                if ($return)
                    return $msg;
                echo $msg;
            }
        }
    }
}

if (!function_exists('input')) {
    function input($key = null, $default = null)
    {
        return Helper::input($key, $default);
    }
}

if (!function_exists('array_get')) {
    function array_get(array $array, $key, $default = null)
    {
        return Helper::array_get($array, $key, $default);
    }
}

if (!function_exists('base_path')) {
    function base_path(string $path = '')
    {
        return Helper::base_path($path);
    }
}

if (!function_exists('app_path')) {
    function app_path(string $path = '')
    {
        return Helper::app_path($path);
    }
}

if (!function_exists('storage_path')) {
    function storage_path(string $path = '')
    {
        return Helper::storage_path($path);
    }
}

if (!function_exists('method')) {
    function method()
    {
        return Helper::method();
    }
}

if (!function_exists('EmailService')) {
    function EmailService()
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new \Framework\Helper\EmailService();
        }
        return $instance;
    }
}

if (!function_exists('db_path')) {
    function db_path()
    {
        return Helper::db_path();
    }
}

if (!function_exists('upload')) {
    function upload($file, $directory, $allowedExtensions = [])
    {
        return Helper::upload($file, $directory, $allowedExtensions);
    }
}

if (!function_exists('auth')) {
    function auth()
    {
        return Helper::auth();
    }
}

if (!function_exists('check')) {
    function check()
    {
        return Helper::check();
    }
}

if (!function_exists('logout')) {
    function logout()
    {
        return Helper::logout();
    }
}

if (!function_exists('flash')) {
    function flash($key, $message = null)
    {
        if ($message === null) {
            return Helper::getFlash($key);
        }
        Helper::flash($key, $message);
        return null;
    }
}

if (!function_exists('getFlash')) {
    function getFlash($key)
    {
        return Helper::getFlash($key);
    }
}


if (!function_exists('dd')) {
    function dd(...$args)
    {
        var_dump(...$args);
        die;
    }
}

if (!function_exists('env')) {
    function env($key, $default = null)
    {
        return $_ENV[$key] ?? $_SERVER[$key] ?? $default;
    }
}


if (!function_exists('redirectWith')) {
    function redirectWith($url, $key, $message = null, $seconds = 0)
    {
        Helper::redirectWith($url, $key, $message, $seconds);
    }
}

// =========================================================
// AUTHENTICATION HELPERS (Universal - Works with any table)
// =========================================================

if (!function_exists('auth')) {
    /**
     * Get Auth instance for a specific table or default user
     * @param string|null $table Table name (default: users)
     * @return \Framework\Helper\Auth|null
     */
    function auth($table = null)
    {
        if ($table === null) {
            return Helper::auth();
        }
        return Helper::auth($table);
    }
}

if (!function_exists('login')) {
    /**
     * Login user to any table
     * @param string $email
     * @param string $password
     * @param string $table Table name (default: users)
     * @param bool $remember Remember me
     * @return object|null
     */
    function login($email, $password, $table = 'users', $remember = false)
    {
        return Helper::login($email, $password, $table, $remember);
    }
}

if (!function_exists('register')) {
    /**
     * Register new user
     * @param array $data User data
     * @param string $table Table name (default: users)
     * @param bool $autoLogin Auto login after register
     * @return object|null
     */
    function register($data, $table = 'users', $autoLogin = true)
    {
        return Helper::register($data, $table, $autoLogin);
    }
}

if (!function_exists('check')) {
    /**
     * Check if user is authenticated
     * @param string $table Table name (default: users)
     * @return bool
     */
    function check($table = 'users')
    {
        return Helper::check($table);
    }
}

if (!function_exists('user')) {
    /**
     * Get authenticated user
     * @param string $table Table name (default: users)
     * @return object|null
     */
    function user($table = 'users')
    {
        return Helper::user($table);
    }
}

if (!function_exists('logout')) {
    /**
     * Logout user
     * @param string $table Table name (default: users)
     * @return void
     */
    function logout($table = 'users')
    {
        Helper::logout($table);
    }
}

if (!function_exists('userId')) {
    /**
     * Get authenticated user ID
     * @param string $table Table name (default: users)
     * @return int|null
     */
    function userId($table = 'users')
    {
        return Helper::userId($table);
    }
}

if (!function_exists('isAdmin')) {
    /**
     * Check if admin is authenticated
     * @return bool
     */
    function isAdmin()
    {
        return Helper::adminCheck();
    } 
}

if (!function_exists('admin')) {
    /**
     * Get authenticated admin
     * @return object|null
     */
    function admin()
    {
        return Helper::adminAuth();
    }
}

if (!function_exists('adminLogin')) {
    /**
     * Login admin
     * @param string $email
     * @param string $password
     * @param bool $remember Remember me
     * @return object|null
     */
    function adminLogin($email, $password, $remember = false)
    {
        return Helper::adminAuthenticate($email, $password);
    }
}

if (!function_exists('adminLogout')) {
    /**
     * Logout admin
     * @return void
     */
    function adminLogout()
    {
        Helper::adminLogout();
    }
}

// =========================================================
// DATABASE HELPER SHORTCUTS
// =========================================================

if (!function_exists('db')) {
    /**
     * Get DB table instance
     * @param string $table Table name
     * @return \Framework\Helper\DB
     */
    function db($table)
    {
        return \Framework\Helper\DB::table($table);
    }
}

if (!function_exists('table')) {
    /**
     * Get DB table instance (alias for db)
     * @param string $table Table name
     * @return \Framework\Helper\DB
     */
    function table($table)
    {
        return \Framework\Helper\DB::table($table);
    }
}

// =========================================================
// MODEL QUERY BUILDER SHORTCUTS
// =========================================================

if (!function_exists('model')) {
    /**
     * Get model instance for query building
     * @param string $model Model class name
     * @return \Framework\Model\Model
     */
    function model($model)
    {
        $class = "App\\Models\\$model";
        if (class_exists($class)) {
            return new $class();
        }
        throw new \Exception("Model class '$class' not found");
    }
}

// =========================================================
// REQUEST HELPER SHORTCUTS
// =========================================================

if (!function_exists('request')) {
    /**
     * Get request input
     * @param string|null $key Key name
     * @param mixed $default Default value
     * @return mixed
     */
    function request($key = null, $default = null)
    {
        return Helper::input($key, $default);
    }
}

if (!function_exists('post')) {
    /**
     * Get POST data
     * @param string|null $key Key name
     * @param mixed $default Default value
     * @return mixed
     */
    function post($key = null, $default = null)
    {
        if ($key === null) {
            return $_POST;
        }
        return $_POST[$key] ?? $default;
    }
}

if (!function_exists('get')) {
    /**
     * Get GET data
     * @param string|null $key Key name
     * @param mixed $default Default value
     * @return mixed
     */
    function get($key = null, $default = null)
    {
        if ($key === null) {
            return $_GET;
        }
        return $_GET[$key] ?? $default;
    }
}

if (!function_exists('has')) {
    /**
     * Check if request has key
     * @param string $key Key name
     * @return bool
     */
    function has($key)
    {
        return isset($_POST[$key]) || isset($_GET[$key]);
    }
}

if (!function_exists('method')) {
    /**
     * Get request method
     * @return string
     */
    function method()
    {
        return Helper::method();
    }
}

if (!function_exists('isAjax')) {
    /**
     * Check if request is AJAX
     * @return bool
     */
    function isAjax()
    {
        return Helper::isAjax();
    }
}

// =========================================================
// RESPONSE HELPER SHORTCUTS
// =========================================================

if (!function_exists('json')) {
    /**
     * Return JSON response
     * @param array $data Data to encode
     * @param int $statusCode HTTP status code
     * @return void
     */
    function json($data, $statusCode = 200)
    {
        Helper::json($data, $statusCode);
    }
}

if (!function_exists('abort')) {
    /**
     * Abort with error
     * @param int $code HTTP status code
     * @param string $message Error message
     * @return void
     */
    function abort($code, $message = '')
    {
        http_response_code($code);
        if (empty($message)) {
            $message = "Error $code";
        }
        echo "<h1>Error $code</h1><p>$message</p>";
        exit;
    }
}

// =========================================================
// UTILITY HELPERS
// =========================================================

if (!function_exists('now')) {
    /**
     * Get current datetime
     * @return string
     */
    function now()
    {
        return date('Y-m-d H:i:s');
    }
}

if (!function_exists('today')) {
    /**
     * Get current date
     * @return string
     */
    function today()
    {
        return date('Y-m-d');
    }
}

if (!function_exists('str_random')) {
    /**
     * Generate random string
     * @param int $length Length of string
     * @return string
     */
    function strRandom($length = 16)
    {
        return bin2hex(random_bytes($length / 2));
    }
}

if (!function_exists('hashPassword')) {
    /**
     * Hash password
     * @param string $password Password to hash
     * @return string
     */
    function hashPassword($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }
}

if (!function_exists('verifyPassword')) {
    /**
     * Verify password
     * @param string $password Password to verify
     * @param string $hash Hash to verify against
     * @return bool
     */
    function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }
}

if (!function_exists('getClientIP')) {
    /**
     * Get client IP address
     * @return string
     */
    function getClientIP()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }
}

if (!function_exists('userAgent')) {
    /**
     * Get user agent
     * @return string
     */
    function userAgent()
    {
        return $_SERVER['HTTP_USER_AGENT'] ?? '';
    }
}

if (!function_exists('referer')) {
    /**
     * Get referer URL
     * @return string
     */
    function referer()
    {
        return $_SERVER['HTTP_REFERER'] ?? '';
    }
}

