<?php

namespace Teapot;

final class Server
{
    public static function ExceptionHandler($e)
    {
        /*
        This function sets the parameters for return on error. If you do not have zedebug the formatting
        of the var dump may be difficult to read.
         */
        self::$status = 'error';
        self::$error  = $e->getCode();
        self::$reason = self::$error . ": " . $e->getMessage();
        self::$info   = [
            'status' => self::$status,
            'error'  => self::$error,
            'reason' => self::$reason,
        ];
        $trace = debug_backtrace();
        if (DISPLAY_ERRORS && ENVIRONMENT !== 'prod') {
            var_dump($e);
        } else {
            self::writeLog([self::$error, $e->getMessage()]);
            self::writeTrace($trace);
        }
        return false;
    }
    public static function ErrorHandler($e, $m, $f, $l)
    {
        // This error_handler gets any type of PHP error from any page that includes this class
        $trace = debug_backtrace();
        if (DISPLAY_ERRORS && ENVIRONMENT !== 'prod') {
            var_dump(['Priority' => $e, 'Message' => $m, 'File' => $f, 'Line' => $l]);
        } else {
            self::writeLog([$m, $f, $l], 'Error Log');
            self::writeTrace($trace);
        }
        return false;
    }

    public static function writeLog($array = [], $fname = '')
    {
        if (!is_array($array)) {
            $array = [$array];
        }
        if (empty($array) || !is_string($fname)) {
            return false;
        }
        // uncertain (may shift to comma seperated)
        if (!is_array($array[0])) {
            $hold    = $array;
            $array   = [];
            $array[] = $hold;
        }

        self::checkLogDir();
        // set the default fname
        $fname   = $fname ?: 'Issue Log';
        $fname   = date('Y-m-d') . ' ' . $fname . '.csv';
        $logFile = fopen(LOG_PATH . $fname, 'a+') or die('Unable to document issue. Please email mentoring_web_feedback@group.apple.com about this issue.');

        foreach ($array as $line) {
            if (!is_array($line)) {
                $line = [$line];
            }
            array_unshift($line, date('Y-m-d H:i:s'));
            fputcsv($logFile, $line);
        }
        fclose($logFile);
        return true;
    }

    private static function writeTrace($array)
    {
        if (!is_array($array)) {
            return false;
        }
        $string = json_encode($array);
        if (strlen($string) === 0) {
            return false;
        }
        self::checkLogDir();
        $date   = new DateTime();
        $prefix = strval($date->format('Y.m.d h.i.s.u'));

        $newTrace = fopen(TRACE_PATH . $prefix . '.json', 'a+') or die('Unable to document issue. Please email mentoring_web_feedback@group.apple.com about this issue.');
        fwrite($newTrace, $string . "\n");
        fclose($newTrace);
        return true;
    }

    private static function checkLogDir()
    {
        // check to ensure the folder for logging exists
        if (!file_exists(LOG_PATH)) {
            mkdir(LOG_PATH, 0777, true);
        }
        if (!file_exists(TRACE_PATH)) {
            mkdir(TRACE_PATH, 0777);
        }

        $fname = LOG_PATH . date('Y-m-d') . ' Error Log' . '.csv';
        if (!file_exists($fname)) {
            $errorLog = fopen($fname, 'a+');
            fputcsv($errorLog, ['Date/Time', 'Message', 'File', 'Line']);
            fclose($errorLog);
        }

        $fname = LOG_PATH . date('Y-m-d') . ' Issue Log' . '.csv';
        if (!file_exists($fname)) {
            $issueLog = fopen($fname, 'a+');
            fputcsv($issueLog, ['Date/Time', 'Error Code', 'Message']);
            fclose($issueLog);
        }
    }
}
