<?php
namespace cafetapi\data;

/**
 * The calendar is an object representing a date
 *
 * @author Damien
 * @since API 1.0.0 (2018)
 */
final class Calendar extends JSONParsable implements Data
{

    private $year;

    private $month;

    private $day;

    private $hour;

    private $mins;

    private $secs;

    /**
     * The calendar is an object representing a date
     *
     * @param int $year
     *            the year in format YYYY
     * @param int $month
     *            the month in format M
     * @param int $day
     *            the in format dd
     * @param int $hour
     *            the hour in format HH
     * @param int $mins
     *            the minutes in format mm
     * @param int $secs
     *            the seconds int format ss
     * @since API 1.0.0 (2018)
     */
    public function __construct(int $year, int $month, int $day, int $hour, int $mins, int $secs)
    {
        $this->year = $year;
        $this->month = $month;
        $this->day = $day;
        $this->hour = $hour;
        $this->mins = $mins;
        $this->secs = $secs;
    }

    /**
     * Returns the year
     *
     * @return int the year
     * @since API 1.0.0 (2018)
     */
    public final function getYear(): int
    {
        return $this->year;
    }

    /**
     * Returns the month
     *
     * @return int the month
     * @since API 1.0.0 (2018)
     */
    public final function getMonth(): int
    {
        return $this->month;
    }

    /**
     * Returns the day
     *
     * @return int the day
     * @since API 1.0.0 (2018)
     */
    public final function getDay(): int
    {
        return $this->day;
    }

    /**
     * Returns the hour
     *
     * @return int the hour
     * @since API 1.0.0 (2018)
     */
    public final function getHour(): int
    {
        return $this->hour;
    }

    /**
     * Returns the minutes
     *
     * @return int the minustes
     * @since API 1.0.0 (2018)
     */
    public final function getMins(): int
    {
        return $this->mins;
    }

    /**
     * Returns the seconds
     *
     * @return int the seconds
     * @since API 1.0.0 (2018)
     */
    public final function getSecs(): int
    {
        return $this->secs;
    }

    public final function getDate(): string
    {
        return $this->year . '-' . $this->month . '-' . $this->day;
    }

    public final function getDateTime(): string
    {
        return $this->year . '-' . $this->month . '-' . $this->day . ($this->hour < 10 ? '0' : '') . $this->hour . ':' . ($this->mins < 10 ? '0' : '') . $this->mins . ':' . ($this->secs < 10 ? '0' : '') . $this->secs;
    }

    public final function getFormatedDate(): string
    {
        return ($this->day < 10 ? '0' : '') . $this->day . '/' . ($this->month < 10 ? '0' : '') . $this->month . '/' . $this->year;
    }

    public final function getFormatedTime(bool $show_secs = false)
    {
        return ($this->hour < 10 ? '0' : '') . $this->hour . ':' . ($this->mins < 10 ? '0' : '') . $this->mins . ($show_secs ? ':' . ($this->secs < 10 ? '0' : '') . $this->secs : '');
    }

    /**
     * Sets the year
     *
     * @param int $year
     *            the year to set
     * @since API 1.0.0 (2018)
     */
    public final function setYear(int $year)
    {
        $this->year = $year;
    }

    /**
     * Sets the month
     *
     * @param int $month
     *            the month to set
     * @since API 1.0.0 (2018)
     */
    public final function setMonth(int $month)
    {
        $this->month = $month;
    }

    /**
     * Sets the day
     *
     * @param int $day
     *            the day to set
     * @since API 1.0.0 (2018)
     */
    public final function setDay(int $day)
    {
        $this->day = $day;
    }

    /**
     * Sets the hour
     *
     * @param int $hour
     *            the hour to set
     * @since API 1.0.0 (2018)
     */
    public final function setHour(int $hour)
    {
        $this->hour = $hour;
    }

    /**
     * Sets the minutes
     *
     * @param int $mins
     *            the minutes to set
     * @since API 1.0.0 (2018)
     */
    public final function setMins(int $mins)
    {
        $this->mins = $mins;
    }

    /**
     * Sets the seconds
     *
     * @param int $secs
     *            the seconds to set
     * @since API 1.0.0 (2018)
     */
    public final function setSecs(int $secs)
    {
        $this->secs = $secs;
    }

    public function __toString(): string
    {
        return $this->parse_JSON(get_object_vars($this));
    }
    
    public function getProperties(): array
    {
        return array_merge(array('type' => get_simple_classname($this)), get_object_vars($this));
    }
}

