<?php

declare(strict_types=1);

namespace ModularityServiceInfo\Helper;

class DateFormatter
{
    /**
     * Format a date range for display
     *
     * @param string $startDateRaw
     * @param string $endDateRaw
     * @param mixed $timezone
     * @return string
     */
    public static function formatDateRange(string $startDateRaw, string $endDateRaw, $timezone = null): string
    {
        if (empty($startDateRaw)) {
            return '';
        }

        if ($timezone === null) {
            $timezone = wp_timezone();
        }

        if (is_string($timezone)) {
            $timezone = new \DateTimeZone($timezone);
        }

        try {
            $startDateTime = new \DateTime($startDateRaw, $timezone);
            $startTimestamp = $startDateTime->getTimestamp();

            if (!empty($endDateRaw)) {
                $endDateTime = new \DateTime($endDateRaw, $timezone);
                $endTimestamp = $endDateTime->getTimestamp();
            } else {
                $endTimestamp = $startTimestamp;
            }
        } catch (\Exception $e) {
            return '';
        }

        $dateFormat = apply_filters('Modularity/ServiceInfo/DateFormat', get_option('date_format'));
        $timeFormat = apply_filters('Modularity/ServiceInfo/TimeFormat', get_option('time_format'));

        $startDay = wp_date('Y-m-d', $startTimestamp);
        $endDay = wp_date('Y-m-d', $endTimestamp);

        if ($startDay === $endDay) {
            if ($startTimestamp === $endTimestamp) {
                return sprintf(
                    '%s %s',
                    wp_date($dateFormat, $startTimestamp),
                    wp_date($timeFormat, $startTimestamp)
                );
            }

            return sprintf(
                '%s %s &ndash; %s',
                wp_date($dateFormat, $startTimestamp),
                wp_date($timeFormat, $startTimestamp),
                wp_date($timeFormat, $endTimestamp)
            );
        }

        return sprintf(
            '%s %s &ndash; %s %s',
            wp_date($dateFormat, $startTimestamp),
            wp_date($timeFormat, $startTimestamp),
            wp_date($dateFormat, $endTimestamp),
            wp_date($timeFormat, $endTimestamp)
        );
    }
}
