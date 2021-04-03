<?php

namespace common\components\helpers;

use DateTime;

class DateHelper {
    /**
     * @return string[]
     */
    public static function getWeekDays() : array
    {
        return [
            1 => 'Понедельник',
            2 => 'Вторник',
            3 => 'Среда',
            4 => 'Четверг',
            5 => 'Пятница',
            6 => 'Суббота',
            7 => 'Воскресенье',
        ];
    }

    /**
     * @param int $dayId
     * @return string
     */
    public static function getDayName(int $dayId): string
    {
        return self::getWeekDays()[$dayId] ?? '';
    }

    /**
     * @return string[]
     */
    public static function getMonths() : array
    {
        return [
            1  => 'Январь',
            2  => 'Февраль',
            3  => 'Март',
            4  => 'Апрель',
            5  => 'Май',
            6  => 'Июнь',
            7  => 'Июль',
            8  => 'Август',
            9  => 'Сентябрь',
            10 => 'Октябрь',
            11 => 'Ноябрь',
            12 => 'Декабрь',
        ];
    }

    /**
     * @param int $monthId
     * @return string
     */
    public static function getMonthName(int $monthId): string
    {
        return self::getMonths()[$monthId] ?? '';
    }

    /**
     * @return array
     */
    public static function getYears() : array
    {
        $result = [];

        if (($startYear = \Yii::$app->params['startYear']) > 0) {
            for ($year = $startYear; $year <= date('Y'); $year++) {
                $result[$year] = $year;
            }
        }

        return $result;
    }

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
        $date = new \DateTime("{$name} this week");
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
     * Возвращает дату первого дня текущего месяца
     * @param bool $toString
     *
     * @return string|DateTime
     */
    public static function getStartOfMonth(bool $toString = true)
    {
        $date = (new \DateTime('first day of this month'));

        return $toString ? $date->format('Y-m-d') : $date;
    }

    /**
     * Возвращает дату последнего дня текущего месяца
     * @param bool $toString
     *
     * @return string|DateTime
     */
    public static function getEndOfMonth(bool $toString = true)
    {
        $date = (new \DateTime('last day of this month'));

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

    /**
     * Возвращает массив с датами начала и конца месяца.
     * Если не заданы месяц или год, используются значения месяца/года от текущей даты.
     *
     * @param int|null $month
     * @param int|null $year
     * @param bool $toString
     * @return array
     */
    public static function getDateRangeByMonth(int $month = null, int $year = null, bool $toString = true) : array
    {
        if (!$month) {
            $month = (int)date('m');
        }
        if (!$year) {
            $year = (int)date('Y');
        }

        $date = new DateTime();
        $date->setDate($year, $month, 1);
        $dateRange = [];
        $dateRange[] = $toString ? $date->format('Y-m-d') : clone($date);
        $date->modify('last day of this month');
        $dateRange[] = $toString ? $date->format('Y-m-d') : $date;

        return $dateRange;
    }

    /**
     * Возвращает количество определенных дней (понедельников, вторников...) в месяце.
     * Если не задан день недели, возвращает количество дней в месяце.
     * Если не заданы месяц или год, используются значения месяца/года от текущей даты.
     * @param int|null $day
     * @param int|null $month
     * @param int|null $year
     *
     * @return int
     */
    public static function countDaysInMonth(int $day = null, int $month = null, int $year = null) : int
    {
        if (!$month) {
            $month = (int)date('m');
        }

        if (!$year) {
            $year = (int)date('Y');
        }

        $monthDays = (int)date("t", mktime(0, 0, 0, $month, 1, $year));
        if (!$day) {
            return $monthDays;
        }
        $result = 0;
        for ($i = 1; $i <= $monthDays; $i++) {
            $weekDay  =  (int)date("N", mktime(0, 0, 0, $month, $i, $year));
            if ($weekDay === $day) {
                $result++;
            }
        }
        return $result;
    }

    /**
     * @deprecated
     * Возвращает список недель (первый - последний день) указанного года.
     * Если не задан год, используется значение года от текущей даты.
     * @param int|null $year
     *
     * @return array
     */
    public static function getWeekList(int $year = null) : array
    {
        if (!$year) {
            $year = (int)date('Y');
        }

        $arr = [];
        $firstDayOfYear = mktime(0, 0, 0, 1, 1, $year);
        $nextMonday     = strtotime('monday', $firstDayOfYear);
        $nextSunday     = strtotime('sunday', $nextMonday);

        $num = 1;
        while (date('Y', $nextMonday) == $year) {
            $arr[$num]  = date('d/m', $nextMonday) . '-' . date('d/m', $nextSunday);
            $nextMonday = strtotime('+1 week', $nextMonday);
            $nextSunday = strtotime('+1 week', $nextSunday);
            $num++;
        }

        return $arr;
    }

    /**
     * @deprecated
     * Возвращает первый и последний дни текущей недели, а так же номер недели.
     * Если не заданы месяц или год, используются значения месяца/года от текущей даты.
     * @param int|null $day
     * @param int|null $month
     * @param int|null $year
     *
     * @return array
     */
    public static function getWeekInfo(int $day = null, int $month = null, int $year = null) : array
    {
        if (!$day) {
            $day = (int)date('d');
        }
        if (!$month) {
            $month = (int)date('m');
        }
        if (!$year) {
            $year = (int)date('Y');
        }
        $today = mktime(0, 0, 0, $month, $day, $year);

        if(date('N', $today) == 1) {
            /* если текущий день понедельник*/
            $monday = strtotime('monday', $today);
        } else {
            /* если текущий день не понедельник*/
            $monday = strtotime('last monday', $today);
        }
        $sunday = strtotime('sunday', $monday);

        /* заполняем результирующий массив */
        $arr['start'] = $monday;
        $arr['end'] = $sunday;
        $arr['num'] = self::getNumberOfWeek($monday, $sunday);

        return $arr;
    }

    /**
     * @deprecated
     * Возвращает номер недели по дате понедельника и воскресенья.
     * @param int $start
     * @param int $end
     *
     * @return int
     */
    public static function getNumberOfWeek(int $start, int $end): int
    {
        $firstDayOfYear = mktime(0, 0, 0, 1, 1, date('Y', $start));
        $nextMonday = strtotime('monday', $firstDayOfYear);
        $nextSunday = strtotime('sunday', $nextMonday);

        $num = 1;
        while (date('Y', $nextMonday) === date('Y', $start)) {
            if ($start === $nextMonday && $end === $nextSunday) {
                return $num;
            }
            $nextMonday = strtotime('+1 week', $nextMonday);
            $nextSunday = strtotime('+1 week', $nextSunday);
            $num++;
        }

        return 0;
    }

    /**
     * @param string|null $start
     * @param string|null $end
     * @param bool $toString
     * @return array
     */
    public static function prepareWeeklyIntervalDates(string $start = null, string $end = null, bool $toString = true): array
    {
        $start = \DateTime::createFromFormat('d.m.Y', $start);
        $end = \DateTime::createFromFormat('d.m.Y', $end);

        if (!$start || !$end || ($end < $start)) {
            $start = DateHelper::getStartOfWeek(null, false);
            $end   = DateHelper::getEndOfWeek(null, false);
        }

        return [
            $toString ? $start->format('Y-m-d') : $start,
            $toString ? $end->format('Y-m-d') : $end,
        ];
    }

    /**
     * @param string|null $start
     * @param string|null $end
     * @param bool $toString
     * @return array
     */
    public static function prepareMonthlyIntervalDates(string $start = null, string $end = null, bool $toString = true): array
    {
        $start = \DateTime::createFromFormat('d.m.Y', $start);
        $end = \DateTime::createFromFormat('d.m.Y', $end);

        if (!$start || !$end || ($end < $start)) {
            $start = DateHelper::getStartOfMonth(false);
            $end   = DateHelper::getEndOfMonth(false);
        }

        return [
            $toString ? $start->format('Y-m-d') : $start,
            $toString ? $end->format('Y-m-d') : $end,
        ];
    }
}