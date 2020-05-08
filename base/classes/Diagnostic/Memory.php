<?php


namespace Express\Diagnostic;


class Memory
{
    protected const UNITS = ["b", "kb", "mb", "gb", "tb"];

    /**
     * @param bool $real
     *
     * @return string
     */
    public function current(bool $real = false) : string
    {
        return $this->format(memory_get_usage($real));
    }

    protected function format(int $bytes, int $precision = 2) : string
    {
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count(static::UNITS) - 1);

        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . " " . static::UNITS[$pow];
    }

    /**
     * @return string
     */
    public function peak(bool $real = false) : string
    {
        return $this->format(memory_get_peak_usage($real));
    }
}