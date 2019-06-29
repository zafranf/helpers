<?php
if (defined('APP_PATH')) {
    if (file_exists(APP_PATH . 'helpers.php')) {
        include APP_PATH . 'helpers.php';
    }
}

if (!function_exists('_server')) {
    /**
     * Alias for $_SERVER
     *
     * @param string $key
     * @return array
     */
    function _server($key = null)
    {
        /* Check $key */
        if (is_null($key)) {
            return $_SERVER;
        }

        /* Check requested string */
        $key = upcase($key);
        if (isset($_SERVER[$key])) {
            return $_SERVER[$key];
        }

        return null;
    }
}

if (!function_exists('_session')) {
    /**
     * Alias for $_SESSION
     *
     * @param string $key
     * @return array
     */
    function _session($key = null)
    {
        /* Check $key */
        if (is_null($key)) {
            return $_SESSION;
        }

        /* Check requested string */
        $key = upcase($key);
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }

        return null;
    }
}

if (!function_exists('_cookie')) {
    /**
     * Alias for $_COOKIE
     *
     * @param string $key
     * @return array
     */
    function _cookie($key = null)
    {
        /* Check $key */
        if (is_null($key)) {
            return $_COOKIE;
        }

        /* Check requested string */
        $key = upcase($key);
        if (isset($_COOKIE[$key])) {
            return $_COOKIE[$key];
        }

        return null;
    }
}

if (!function_exists('_input')) {
    /**
     * Alias for $_REQUEST
     *
     * @param string $key
     * @param boolean $int
     * @return array
     */
    function _input($key = null, $default = null)
    {
        /* Check $key */
        if (is_null($key)) {
            return $_REQUEST;
        }

        /* Check requested string */
        if (isset($_REQUEST[$key])) {
            return $_REQUEST[$key];
        }

        return $default;
    }
}

if (!function_exists('_get')) {
    /**
     * Alias for $_GET
     *
     * @param string $key
     * @param boolean $int
     * @return array
     */
    function _get($key = null, $default = null)
    {
        /* Check $key */
        if (is_null($key)) {
            return $_GET;
        }

        /* Check requested string */
        if (isset($_GET[$key])) {
            return $_GET[$key];
        }

        return $default;
    }
}

if (!function_exists('_post')) {
    /**
     * Alias for $_POST
     *
     * @param string $key
     * @param boolean $int
     * @return array
     */
    function _post($key = null, $default = null)
    {
        /* Check $key */
        if (is_null($key)) {
            return $_POST;
        }

        /* Check requested string */
        if (isset($_POST[$key])) {
            return $_POST[$key];
        }

        return $default;
    }
}

if (!function_exists('_files')) {
    /**
     * Alias for $_FILES
     *
     * @param string $key
     * @return array
     */
    function _files($key = null)
    {
        /* rearrange files */
        $_FILES = _rearrange_files();

        /* Check $key */
        if (is_null($key)) {
            return $_FILES;
        }

        /* Check requested string */
        if (isset($_FILES[$key])) {
            return _file($key);
        }

        return null;
    }
}

if (!function_exists('_file')) {
    /**
     * Get file detail in $_FILES
     *
     * @param string $name
     * @return array
     */
    function _file($name)
    {
        $fl = null;

        /* Check requested file */
        if (!is_array($name) && isset($_FILES[$name])) {
            $file = $_FILES[$name];
        }

        /* Mapping file */
        if (isset($file['name']) && $file['name'] != "" && $file['error'] == 0) {
            $xname = explode(".", $file['name']);
            $fl = [];
            $fl['filename'] = $file['name'];
            $fl['name'] = str_replace('.' . end($xname), "", $file['name']);
            $fl['ext'] = '.' . end($xname);
            $fl['tmp'] = $file['tmp_name'];
            $fl['size'] = round($file['size'] / 1024, 2); //in KB
            $fl['mime'] = mime_content_type($fl['tmp']);

            /* Get image dimension */
            $mime = explode("/", $fl['mime'])[0];
            if ($mime == "image" || in_array($fl['ext'], ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp'])) {
                $info = getimagesize($fl['tmp']);
                $fl['width'] = $info[0];
                $fl['height'] = $info[1];
            }
        }

        return $fl;
    }
}

if (!function_exists('_rearrange_files')) {
    /**
     * Rearrange recursive $_FILES
     * http://php.net/manual/en/features.file-upload.multiple.php#118180
     *
     * @return array
     */
    function _rearrange_files()
    {
        $walker = function ($files, $fileInfokey, callable $walker) {
            $ret = [];
            foreach ($files as $k => $v) {
                if (is_array($v)) {
                    $ret[$k] = $walker($v, $fileInfokey, $walker);
                } else {
                    $ret[$k][$fileInfokey] = $v;
                }
            }
            return $ret;
        };

        $files = [];
        foreach ($_FILES as $name => $values) {
            /* init for array_merge */
            if (!isset($files[$name])) {
                $files[$name] = [];
            }
            if (!is_array($values['error'])) {
                /* normal syntax */
                $files[$name] = $values;
            } else {
                /* html array feature */
                foreach ($values as $fileInfoKey => $subArray) {
                    $files[$name] = array_replace_recursive($files[$name], $walker($subArray, $fileInfoKey, $walker));
                }
            }
        }

        return $files;
    }
}

if (!function_exists('debug')) {
    /**
     * Debugging variable
     *
     * @return void
     */
    function debug()
    {
        array_map(function ($data) {
            echo "<pre>";
            print_r($data);
            echo "</pre>";
        }, func_get_args());

        die();
    }
}

if (!function_exists('slug')) {
    /**
     * Convert string to slug
     *
     * @param string $str
     * @param string $separator
     * @return string
     */
    function slug($str, $separator = "-")
    {
        $slug = lowcase($str);
        $slug = preg_replace('([\s\W\_]+)', $separator, $slug);

        return $slug;
    }
}

if (!function_exists('cut')) {
    /**
     * Cut some text
     *
     * @param string $text
     * @param integer $start
     * @param integer $end
     * @param string $separator
     * @return string
     */
    function cut($text, $start = 50, $end = 5, $separator = "...")
    {
        $min = $start + $end;
        if (strlen($text) > $min) {
            $head = substr($text, 0, $start);
            $tail = substr($text, -$end);

            $text = $head . $separator . $tail;
        }

        return $text;
    }
}

if (!function_exists('spaces')) {
    /**
     * Undocumented function
     *
     * @param integer $n
     * @param string $space
     * @return void
     */
    function spaces($n = 4, $space = "&nbsp;")
    {
        return str_repeat($space, $n);
    }
}

if (!function_exists('sanitize')) {
    /**
     * Sanitize string
     */
    function sanitize($string)
    {
        return trim(e($string));
    }
}

if (!function_exists('nf')) {
    /**
     * Alias for number_format
     *
     * @param integer $num
     * @param integer $digit
     * @param string $coms
     * @param string $dots
     * @return string
     */
    function nf($num, $digit = 0, $coms = ",", $dots = ".")
    {
        return number_format($num, $digit, $coms, $dots);
    }
}

if (!function_exists('redirect')) {
    /**
     * Alias for header location
     *
     * @param string $url
     * @return void
     */
    function redirect($url = "/")
    {
        header("location: " . $url);
        die();
    }
}

if (!function_exists('response')) {
    /**
     * Undocumented function
     *
     * @param [type] $data
     * @param integer $statusCode
     * @param boolean $json
     * @return void
     */
    function response($data, $statusCode = 200, $json = true)
    {
        http_response_code($statusCode);
        if ($json) {
            header('Content-Type: application/json');
            $data = json_encode($data);
        }

        die($data);
    }
}

if (!function_exists('upcase')) {
    /**
     * Alias for strtoupper
     *
     * @param string $str
     * @return string
     */
    function upcase($str = "")
    {
        return strtoupper($str);
    }
}

if (!function_exists('lowcase')) {
    /**
     * Alias for strtolower
     *
     * @param string $str
     * @return string
     */
    function lowcase($str = "")
    {
        return strtolower($str);
    }
}

if (!function_exists('url')) {
    /**
     * Create url
     *
     * @param string $url
     * @param boolean $full
     * @return string
     */
    function url($url = "", $pars = [])
    {
        /* validas8 */
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            return $url;
        }

        /* set variable */
        $http = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? 'https' : 'http';
        if (_server('HTTP_CF_VISITOR') !== null) {
            $cf = json_decode(_server('HTTP_CF_VISITOR'));
            if (isset($cf->scheme)) {
                $http = $cf->scheme;
            }
        }
        $scheme = $secure ? 'https' : $http;
        $server_host = _server('SERVER_NAME') . '/';
        if (!filter_var($server_host, FILTER_VALIDATE_URL)) {
            $server_host = _server('HTTP_HOST') . '/';
        }
        $uri = ($url != "/") ? ltrim($url, '/') : '';

        return sprintf("%s://%s%s", $scheme, $server_host, $uri);
    }
}

if (!function_exists('bool')) {
    /**
     * Convert string to boolean
     *
     * @param string $str
     * @return boolean
     */
    function bool($str = "")
    {
        $true = ['true', 't', 'yes', 'y', '1', 'on'];

        if (is_string($str) || is_int($str) || is_bool($str)) {
            $str = strtolower(trim($str));

            return in_array($str, $true);
        }

        return false;
    }
}

if (!function_exists('is_json')) {
    /**
     * Validate string to json
     *
     * @param string $data
     * @return boolean
     */
    function is_json($data = null)
    {
        if (!is_null($data)) {
            @json_decode($data);
            return (json_last_error() === JSON_ERROR_NONE);
        }

        return false;
    }
}

if (!function_exists('load_image')) {
    /**
     * Generate image tag
     *
     * @param string $file
     * @param array $attributes
     * @return string
     */
    function _load_image($file = "", $attributes = [])
    {
        if (file_exists(public_path($file))) {
            $mtime = filemtime(public_path($file));
            $attr = '';
            if (!empty($attributes)) {
                foreach ($attributes as $key => $value) {
                    $attr .= ' ' . $key . '="' . $value . '"';
                }
            }

            return '<img src="' . url($file) . '?' . $mtime . '"' . $attr . '>';
        }
    }
}

if (!function_exists('load_css')) {
    /**
     * Generate link stylesheet tag
     *
     * @param string $file
     * @param array $attributes
     * @return string
     */
    function _load_css($file = "", $attributes = [])
    {
        if (file_exists(public_path($file))) {
            $mtime = filemtime(public_path($file));
            $attr = ' rel="stylesheet" type="text/css"';
            if (!empty($attributes)) {
                $attr = '';
                foreach ($attributes as $key => $value) {
                    $attr .= ' ' . $key . '="' . $value . '"';
                }
            }

            return '<link href="' . url($file) . '?' . $mtime . '"' . $attr . '>';
        }
    }
}

if (!function_exists('load_js')) {
    /**
     * Generate script tag
     *
     * @param string $file
     * @param array $attributes
     * @return string
     */
    function _load_js($file = "", $attributes = [])
    {
        if (file_exists(public_path($file))) {
            $mtime = filemtime(public_path($file));
            $attr = ' type="text/javascript"';
            if (!empty($attributes)) {
                $attr = '';
                foreach ($attributes as $key => $value) {
                    $attr .= ' ' . $key . '="' . $value . '"';
                }
            }

            return '<script src="' . url($file) . '?' . $mtime . '"' . $attr . '></script>';
        }
    }
}

if (!function_exists('public_path')) {
    /**
     * Get public folder path
     *
     * @param string $file
     * @return string
     */
    function public_path($file = "")
    {
        if (defined('PUBLIC_PATH')) {
            return PUBLIC_PATH . $file;
        }

        return null;
    }
}

if (!function_exists('storage_path')) {
    /**
     * Get storage folder path
     *
     * @param string $file
     * @return string
     */
    function storage_path($file = "")
    {
        if (defined('STORAGE_PATH')) {
            return STORAGE_PATH . $file;
        }

        return null;
    }
}
