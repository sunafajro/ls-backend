<?php

namespace app\components\helpers;

use DateTime;

class DateHelper {
    /**
     * Возвращает дату дня текущей недели по его имени.
     * Принимает смещение относительно текущей недели: +/- количество недель.
     * @param string   $name
     * @param int|null $weekNum
     * @param bool     $toString
     * 
     * @return string|DateTime
     */
    public static function getWeekDateByDayName(string $name, int $weekNum = null, bool $toString = true)
    {
        $date = (new \DateTime())->modify("{$name} this week");
        if ($weekNum) {
            if ($weekNum > 0) {
                $weekNum = "+{$weekNum}";
            } else if ($weekNum < 0) {
                $weekNum = (string)$weekNum;
            }
            $date->modify("{$weekNum} weeks");
        } 
        return $toString ? $date->format('Y-m-d') : $date;
    }

    /**
     * Возвращает дату первого дня недели (понедельник)
     * Принимает смещение относительно текущей недели: +/- количество недель.
     * @param int|null $weekNum
     * @param bool     $toString
     * 
     * @return string|DateTime
     */
    public static function getStartOfWeek(int $weekNum = null, bool $toString = true)
    {
        $date = self::getWeekDateByDayName('monday', $weekNum, false);     

        return $toString ? $date->format('Y-m-d') : $date;
    }

    /**
     * Возвращает дату последнего дня недели (воскресенье)
     * Принимает смещение относительно текущей недели: +/- количество недель.
     * @param int|null $weekNum
     * @param bool     $toString
     * 
     * @return string|DateTime
     */
    public static function getEndOfWeek(int $weekNum = null, bool $toString = true)
    {
        $date = self::getWeekDateByDayName('sunday', $weekNum, false);     

        return $toString ? $date->format('Y-m-d') : $date;
    }

    /**
     * Возвращает разницу между двумя датами.
     * Принимает строчный интервал ('12:00 - 13:00', '2020-01-01:2020-01-02').
     * Величины возвращаемых значений d - дни, h - часы, m - минуты, s - секунды. 
     * @param string $str
     * @param string $delimiter
     * @param string $format
     * @param string $unit
     * 
     * @return float|null
     */
    public static function strIntervalToCount(string $str, string $delimiter = ':', string $format = 'Y-m-d', string $unit = 'd')
    {
        $dates = explode($delimiter, $str);
        $start = \DateTime::createFromFormat($format, $dates[0] ?? null);
        $end = \DateTime::createFromFormat($format, $dates[1] ?? null);

        if ($start && $end) {
            $interval = $start->diff($end);
            $seconds = $interval->s + ($interval->i * 60) + ($interval->h * 60 * 60) + ($interval->d * 24 * 60 * 60);
            switch ($unit) {
                case 'm': return $seconds / 60;
                case 'h': return $seconds / 60 / 60;
                case 'd': return $seconds / 60 / 60 / 24;
                default: return $seconds;
            }
        } else {
            return null;
        }
    } 
}