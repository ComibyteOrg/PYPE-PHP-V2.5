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
    function flash($key, $message)
    {
        return Helper::flash($key, $message);
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
