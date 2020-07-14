<?php

namespace Agilize\LaravelDataMapper\Helpers;

class StringHelper
{
    /**
     * Convert words with hyphen or underscore to camelCase
     * pattern.
     *
     * @param  $string
     * @return mixed|string|null
     */
    public static function transformWithHyphenOrUnderScoreToCapitalized($string)
    {
        $string = strtolower($string);

        if (strpos($string, '-') !== false) {
            $split = explode('-', $string);
            return array_reduce(
                $split,
                function ($buffer, $string) {
                    return $buffer .= ucfirst($string);
                }
            );
        }
        if (strpos($string, '_') !== false) {
            $split = explode('_', $string);
            return array_reduce(
                $split,
                function ($buffer, $string) {
                    return $buffer .= ucfirst($string);
                }
            );
        }
        return ucfirst($string);
    }

    /**
     * Check if a given string is a valid UUID
     *
     * @param  string $uuid
     * @return boolean
     */
    public static function isValidUuid($uuid)
    {
        if (!is_string($uuid) || (preg_match('/^[a-f\d]{8}(-[a-f\d]{4}){4}[a-f\d]{8}$/i', $uuid) !== 1)) {
            return false;
        }

        return true;
    }

    /**
     * Check if a given param is a valid numeric ID
     *
     * @param  int $id
     * @return boolean
     */
    public static function isValidNumericId($id)
    {
        if (!is_numeric($id)) {
            return false;
        }

        return true;
    }
}
