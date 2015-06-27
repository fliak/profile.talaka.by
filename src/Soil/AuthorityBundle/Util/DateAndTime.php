<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 29.5.15
 * Time: 23.20
 */

namespace Soil\AuthorityBundle\Util;


class DateAndTime {


    public static function getTimeAgo($date, $opposite = 'now')    {
        $time1 = self::convertDateToTimestamp($date);

        $time2 = self::convertDateToTimestamp($opposite);

        $diff = $time1 - $time2;

        return $diff;
    }

    /**
     * @param $date
     *
     * @throws \Exception
     *
     * @return int timestamp
     */
    public static function convertDateToTimestamp($date)    {
        if (is_string($date))   {
            return strtotime($date);
        }
        elseif (is_numeric($date)) {
            return $date;
        }
        elseif (is_object($date) && $date instanceof \DateTime) {
            return $date->getTimestamp();
        }
        else    {
            $given = gettype($date);
            throw new \Exception("Please provide timestamp, formatted date or DateTime object. $given given.");
        }
    }


    /**
     * @param $time
     * @param string $to
     * @param bool $modulo
     * @return float|array
     * @throws \Exception
     */
    public static function convertTimeTo($time, $to = 'hour', $modulo = false)   {
        $convertTargets = [
            'minute' => 60,
            'hour' => 3600,
            'day' => 86400,
            'week' => 604800,
            'month' => 2592000,
            'year' => 31536000
        ];

        if (!array_key_exists($to, $convertTargets)) {
            $keys = implode(', ', array_keys($convertTargets));
            throw new \Exception("Cannot convert to `$to`. Only $keys supported.");
        }

        $denominator =  $convertTargets[$to];

        $value = $time / $denominator;

        if ($modulo)    {
            $intPart = intval($value);
            $mantissa = $value - $intPart;

            return [$intPart, $mantissa * $denominator];
        }
        else    {
            return $value;
        }
    }

} 