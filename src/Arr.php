<?php

namespace Cleup\Helpers;

class Arr
{
    /**
     * Add to the end of the array at the given value, using "dot" notation.
     *
     * @param string|int $key - Dot syntax
     * @param mixed $value
     * @param array $arr
     * @return void
     */
    public static function append(
        string|int $key,
        mixed $value,
        array &$arr
    ): void {
        $key = (string)$key;

        if (($keys = explode('.', $key)) && count($keys)) {
            $data = static::get($key, $arr);

            if (!isset($data))
                $result = [$value];
            else {
                $result = !is_array($data) ? [$data] : $data;
                $result[] = $value;
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
    public static function prepend(
        string|int $key,
        mixed $value,
        array &$arr
    ): void {
        $key = (string)$key;

        if (($keys = explode('.', $key)) && count($keys)) {
            $data = static::get($key, $arr);

            if (!isset($data))
                static::set($key, [$value], $arr);
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
     * @return array
     */
    public static function replace(
        string|int $key,
        mixed $value,
        array &$arr
    ): array {
        $key = (string)$key;

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
    public static function set(
        string|int $key,
        mixed $value,
        array &$arr
    ): void {
        $key = (string)$key;

        if (strpos($key, '.') !== false && ($keys = explode('.', $key)) && count($keys)) {
            while (count($keys) > 1) {
                $key = array_shift($keys);

                if (!isset($arr[$key]) || !is_array($arr[$key]))
                    $arr[$key] = [];

                $arr = &$arr[$key];
            }

            $arr[array_shift($keys)] = $value;
        } else {
            $arr[$key] = $value;
        }
    }

    /**
     * Remove an array element from a given array using dot notation.
     *
     * @param string|int $key - Dot syntax
     * @param array $arr
     * @return void
     */
    public static function delete(
        string|int $key,
        array &$arr
    ): void {
        $key = (string)$key;
        $keys = explode('.', $key);

        if (count($keys) === 1) {
            unset($arr[$key]);
            return;
        }

        $temp = &$arr;
        $lastKey = array_pop($keys);

        foreach ($keys as $part) {
            if (!isset($temp[$part]) || !is_array($temp[$part])) {
                return;
            }
            $temp = &$temp[$part];
        }

        unset($temp[$lastKey]);
    }

    /**
     * Check if an item or items exist in an array using "dot" notation.
     *
     * @param string|int $key - Dot syntax
     * @param array $arr
     * @return bool
     */
    public static function has(
        string|int $key,
        array $arr
    ): bool {
        $key = (string)$key;

        if (count(($keys = explode('.', $key)))) {
            foreach ($keys as $itemKey) {
                if (!isset($arr[$itemKey]))
                    return false;

                $arr = $arr[$itemKey];
            }

            return true;
        }

        return isset($arr[$key]);
    }

    /**
     * Recursively get the value of the array
     * 
     * @param string|int $key - Dot syntax
     * @param array $arr
     * @param mixed $default
     * @return mixed
     */
    public static function get(
        string|int $key,
        array $arr,
        mixed $default = null
    ): mixed {
        $key = (string)$key;

        if (strpos($key, '.') !== false && count(($keys = explode('.', $key)))) {
            foreach ($keys as $itemKey) {
                if (!isset($arr[$itemKey]))
                    return $default;

                $arr = $arr[$itemKey];
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
    public static function isAssoc(array $array): bool
    {
        return !array_is_list($array);
    }

    /**
     * Determines if an array is a list.
     *
     * @param array $array
     * @return bool
     */
    public static function isList(array $array): bool
    {
        return array_is_list($array);
    }

    /**
     * Matching for each of the array elements.
     *
     * @param array $array
     * @param callable $callback
     * @return array
     */
    public static function map(
        array $array,
        callable $callback
    ): array {
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
     * @param array $array
     * @return string
     */
    public static function query(array $array): string
    {
        return http_build_query($array, '', '&', PHP_QUERY_RFC3986);
    }

    /**
     * Divide the array into keys and values.
     *
     * @param array $array
     * @return array
     */
    public static function divide(array $array): array
    {
        return [array_keys($array), array_values($array)];
    }

    /**
     * Join all items using a string.
     *
     * @param array $array
     * @param string $glue
     * @param string $finalGlue
     * @return string
     */
    public static function join(
        array $array,
        string $glue,
        string $finalGlue = ''
    ): string {
        if ($finalGlue === '')
            return implode($glue, $array);

        if (count($array) === 0)
            return '';

        if (count($array) === 1)
            return (string)end($array);

        $finalItem = array_pop($array);

        return implode($glue, $array) . $finalGlue . $finalItem;
    }

    /**
     * Retrieve only the required elements from the specified array.
     *
     * @param array $array
     * @param array|string $keys
     * @return array
     */
    public static function only(
        array $array,
        array|string $keys
    ): array {
        return array_intersect_key(
            $array,
            array_flip((array) $keys)
        );
    }
}
