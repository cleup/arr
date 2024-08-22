<?php

namespace Cleup\Helpers;

class Arr
{
    /**
     * Add to the end of the array at the given value, using "dot" notation.
     *
     * @param string|int $key - Dot syntax
     * @param mixed $value
     * @param array $array
     * @return void
     */
    public static function push($key, $value, &$arr)
    {
        if (($keys = explode('.', $key)) && count($keys)) {
            $data = static::get($key, $arr);

            if (!isset($data))
                $result = array($value);
            else {
                $result = !is_array($data) ? [$data] : $data;
                array_push($result, $value);
            }

            static::set($key, $result, $arr);
        }
    }

    /**
     * Add to the beginning of the array at the given value, using "dot" notation.
     *
     * @param string|int $key - Dot syntax
     * @param mixed $value
     * @param array $arr
     * @return void
     */
    public static function unshift($key, $value, &$arr)
    {
        if (($keys = explode('.', $key)) && count($keys)) {
            $data = static::get($key, $arr);

            if (!isset($data))
                static::set($key, array($value), $arr);
            else {
                $result = !is_array($data) ? [$data] : $data;
                array_unshift($result, $value);
                static::set($key, $result, $arr);
            }
        }
    }

    /**
     * Replaces the primary value with a secondary value using "dot" notation.
     *
     * @param string|int $key - Dot syntax
     * @param mixed $value
     * @param array $arr
     * @return void
     */
    public static function replace($key, $value, &$arr)
    {
        if (($keys = explode('.', $key)) && count($keys)) {
            $data = static::get($key, $arr);

            if (!isset($data))
                static::set($key, $value, $arr);
            else {
                if (!is_array($value))
                    static::set($key, $value, $arr);
                else {
                    static::set(
                        $key,
                        array_replace_recursive(
                            $data,
                            $value
                        ),
                        $arr
                    );
                }
            }
        }

        return $arr;
    }

    /**
     * Set an array item to a given value using "dot" notation.
     *
     * @param string|int $key - Dot syntax
     * @param mixed $value
     * @param array $arr
     * @return void
     */
    public static function set($key, $value, &$arr)
    {
        if (strpos($key, '.') !== false && ($keys = explode('.', $key)) && count($keys)) {
            while (count($keys) > 1) {
                $key = array_shift($keys);

                if (!isset($arr[$key]) || !is_array($arr[$key]))
                    $arr[$key] = [];

                $arr = &$arr[$key];
            }

            $arr[array_shift($keys)] = $value;
        } else
            $arr[$key] = $value;
    }

    /**
     * Remove an array element from a given array using dot notation.
     *
     * @param string|int $key - Dot syntax
     * @param array $arr
     * @return void
     */
    public static function delete($key, &$arr)
    {
        if (($keys = explode('.', $key)) && count($keys)) {
            while (count($keys) > 1) {
                $arr = &$arr[array_shift($keys)];
            }

            if (static::has($key, $arr))
                unset($arr[array_shift($keys)]);
        } else {
            if (static::has($key, $arr))
                unset($arr[$key]);
        }
    }

    /**
     * Check if an item or items exist in an array using "dot" notation.
     *
     * @param string $keys - Dot syntax
     * @param array $arr
     * @return bool
     */
    public static function has($key, $arr)
    {
        if (count(($keys = explode('.', $key)))) {
            foreach ($keys as $key) {
                if (!isset($arr[$key]))
                    return false;

                $arr = $arr[$key];
            }

            return true;
        }

        return isset($arr[$key]);
    }

    /**
     * Recursively get the value of the array
     * 
     * @param string $key - Dot syntax
     * @param array $arr
     * @param mixed $default
     * @return mixed
     */
    public static function get($key, $arr, $default = null)
    {
        if (strpos($key, '.') !== false && count(($keys = explode('.', $key)))) {
            foreach ($keys as $key) {
                if (!isset($arr[$key]))
                    return $default;

                $arr = $arr[$key];
            }

            return $arr;
        }

        return isset($arr[$key]) ? $arr[$key] : $default;
    }

    /**
     * Determines if an array is associative.
     *
     * @param array $array
     * @return bool
     */
    public static function isAssoc($array)
    {
        return !array_is_list($array);
    }

    /**
     * Determines if an array is a list.
     *
     * @param array $array
     * @return bool
     */
    public static function isList($array)
    {
        return array_is_list($array);
    }

    /**
     * Matching for each of the array elements.
     *
     * @param  array  $array
     * @param  callable  $callback
     * @return array
     */
    public static function map(array $array, callable $callback)
    {
        $keys = array_keys($array);

        try {
            $items = array_map($callback, $array, $keys);
        } catch (\ArgumentCountError) {
            $items = array_map($callback, $array);
        }

        return array_combine($keys, $items);
    }

    /**
     * Convert the array into a query string.
     *
     * @param  array  $array
     * @return string
     */
    public static function query($array)
    {
        return http_build_query($array, '', '&', PHP_QUERY_RFC3986);
    }

    /**
     * Divide the array into keys and values.
     *
     * @param array $array
     * @return array
     */
    public static function divide($array)
    {
        return [array_keys($array), array_values($array)];
    }

    /**
     * Join all items using a string.
     *
     * @param array  $array
     * @param string $glue
     * @param string $finalGlue
     * @return string
     */
    public static function join($array, $glue, $finalGlue = '')
    {
        if ($finalGlue === '')
            return implode($glue, $array);

        if (count($array) === 0)
            return '';

        if (count($array) === 1)
            return end($array);

        $finalItem = array_pop($array);

        return implode($glue, $array) . $finalGlue . $finalItem;
    }

    /**
     * Write array to file
     * 
     * @param array $array
     * @param string $filePath
     * @param array $params
     * @param callable|bool $callback
     * @return bool
     */
    public static function write($array, $filePath, $params = array(), $callback = false)
    {
        $params = array_merge(array(
            'variable' => false,
            'write' => true,
            'comment' => '',
            'debug' => false,
            'chmod' => false,
            'chmodDir' => 0775
        ), $params);
        $status = true;
        $comment = $params["comment"] ? "/* " . $params["comment"] . " */\n" : '';
        $export  = var_export($array, true);
        $content = preg_replace("/=> \n /", "=>", $export);
        $content = str_replace("  ", "    ", $content);
        $content = str_replace('array (', 'array(', $content);
        $content = preg_replace("/=> \n /", "=>", $content);
        $content = preg_replace("/\s+array\(/", " " . 'array(', $content);
        $content = preg_replace_callback('/,\s+\)/', function ($m) {
            $space =  "";
            $cur = strlen($m[0]) - 3;

            if ($cur > 0)
                for ($i = 1; $i <= $cur; $i++)
                    $space .= " ";

            return  "\n"  . $space . ")";
        }, $content);

        $entryPoint = (isset($params['variable']) && $params['variable'] !== false) ?
            (!empty($params['variable']) ?
                "\$" . $params['variable'] . " = " :
                '') :
            'return ';

        $content = "<?php\n\n"  . $comment . $entryPoint . $content . ';';

        if ($params['write']) {
            $dir = dirname($filePath);

            if (!is_dir($dir)) {
                if (!@mkdir($dir, $params['chmodDir'], true)) {
                    if ($params['debug'])
                        throw new \Exception('Failed to create directory: ' . $dir);

                    $status = false;
                }
            }

            if ($status && !@file_put_contents($filePath, $content)) {
                if ($params['debug'])
                    throw new \Exception('Failed to create file: ' . $filePath);

                $status = false;
            }

            if ($params['chmod'] !== false && file_exists($filePath))
                chmod($filePath, $params['chmod']);
        }

        if (is_callable($callback))
            $callback($content, $array, $filePath, $status);

        return $status;
    }
}
