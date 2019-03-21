<?php
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
     * @return void
     */
    function slug($str, $separator = "-")
    {
        $slug = lowcase($str);
        $slug = preg_replace('([\s\W\_]+)', $separator, $slug);

        return $slug;
    }
}

if (!function_exists('cutText')) {
    /**
     * Cut some text
     *
     * @param string $text
     * @param integer $start
     * @param integer $end
     * @param string $separator
     * @return void
     */
    function cutText($text, $start = 50, $end = 5, $separator = "...")
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

if (!function_exists('nf')) {
    /**
     * Alias for number_format
     *
     * @param integer $num
     * @param integer $digit
     * @param string $coms
     * @param string $dots
     * @return void
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

if (!function_exists('_server')) {
    /**
     * Alias for $_SERVER
     *
     * @param string $key
     * @return void
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
     * @return void
     */
    function _session($key = null)
    {
        /* Check $key */
        if (is_null($key)) {
            return $_SESSION;
        }

        /* Check requested string */
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
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
     * @return void
     */
    function _input($key = null, $int = false)
    {
        /* Check $key */
        if (is_null($key)) {
            return $_REQUEST;
        }

        /* Check requested string */
        if (isset($_REQUEST[$key])) {
            $val = $_REQUEST[$key];

            /* Make it as integer if true */
            if ($int) {
                return (int) $val;
            }

            return $val;
        }

        return null;
    }
}

if (!function_exists('_get')) {
    /**
     * Alias for $_GET
     *
     * @param string $key
     * @param boolean $int
     * @return void
     */
    function _get($key = null, $int = false)
    {
        /* Check $key */
        if (is_null($key)) {
            return $_GET;
        }

        /* Check requested string */
        if (isset($_GET[$key])) {
            $val = $_GET[$key];

            /* Make it as integer if true */
            if ($int) {
                return (int) $val;
            }

            return $val;
        }

        return null;
    }
}

if (!function_exists('_post')) {
    /**
     * Alias for $_POST
     *
     * @param string $key
     * @param boolean $int
     * @return void
     */
    function _post($key = null, $int = false)
    {
        /* Check $key */
        if (is_null($key)) {
            return $_POST;
        }

        /* Check requested string */
        if (isset($_POST[$key])) {
            $val = $_POST[$key];

            /* Make it as integer if true */
            if ($int) {
                return (int) $val;
            }

            return $val;
        }

        return null;
    }
}

if (!function_exists('_files')) {
    /**
     * Alias for $_FILES
     *
     * @param string $key
     * @return void
     */
    function _files($key = null)
    {
        /* rearrange files */
        $_FILES = rearrangeFiles();

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
     * @return void
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

if (!function_exists('rearrangeFiles')) {
    /**
     * Rearrange recursive $_FILES
     * http://php.net/manual/en/features.file-upload.multiple.php#118180
     *
     * @return void
     */
    function rearrangeFiles()
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

if (!function_exists('upcase')) {
    /**
     * Alias for strtoupper
     *
     * @param string $str
     * @return void
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
     * @return void
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
     * @return void
     */
    function url($url = "", $pars = [])
    {
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            return $url;
        }
        
        return sprintf(
            "%s://%s%s%s",
            isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
            $_SERVER['SERVER_NAME'] . '/',
            $url != "/" ? ltrim($url, '/') : '',
            !empty($pars) ? '?' . http_build_query($pars) : ''
        );
    }
}

if (!function_exists('bool')) {
    /**
     * Convert string to boolean
     *
     * @param string $str
     * @return void
     */
    function bool($str = "")
    {
        $true = ['true', 't', 'yes', 'y', '1', 'on'];

        if (is_string($str) || is_int($str)) {
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

if (!function_exists('loadCSS')) {
    /**
     * Generate link stylesheet tag
     *
     * @param string $file
     * @return void
     */
    function loadCSS($file = "")
    {
        if (file_exists(public_path($file))) {
            $mtime = filemtime(public_path($file));

            return '<link href="' . url($file) . '?' . $mtime . '" rel="stylesheet">';
        }
    }
}

if (!function_exists('loadJS')) {
    /**
     * Generate script tag
     *
     * @param string $file
     * @param boolean $async
     * @return void
     */
    function loadJS($file = "", $async = false)
    {
        if (file_exists(public_path($file))) {
            $mtime = filemtime(public_path($file));
            $async = ($async) ? 'async' : '';

            return '<script src="' . url($file) . '?' . $mtime . '" ' . $async . '></script>';
        }
    }
}

if (!function_exists('public_path')) {
    /**
     * Get public folder path
     *
     * @param string $tofile
     * @return void
     */
    function public_path($tofile = "")
    {
        return PUBLIC_PATH . $tofile;
    }
}
