<?php

namespace Teapot;

class Video
{
    const VIDEO_PATH = '/videos/';
    const BUFFER     = 102400; // 100 kb
    public $state    = null;
    public $error    = null;
    private $video;
    public $fileSize     = null;
    public $streamBlocks = null;
    public $lastModified = null;
    final public function __construct($fileName = null)
    {
        if ($fileName) {
            $this->fetch($fileName);
        }
    }
    public function fetch($fileName = null)
    {
        // check the filename integrity
        if (!$fileName || !is_string($fileName)) {
            throw new Exception('No file name provided.');
        }
        // set the path to the video and open it in read only state (binary)
        $filePath    = $_SERVER['DOCUMENT_ROOT'] . self::VIDEO_PATH . $fileName;
        $this->video = fopen($filePath, 'rb') or die('Could not open video file for reading.');

        // setup information about the file and return the instance
        $fileSize           = $this->fileSize           = filesize($filePath);
        $this->lastModified = filemtime($filePath);
        $this->streamBlocks = ceil($fileSize / self::BUFFER * 2 + 4);
    }
    /*
     * Start streaming video content
     */
    public function play($type = 'video/mp4')
    {
        // flush everything if possible and setup the basic headers
        ob_get_clean();
        header('Content-Type: ' . $type);
        header('Cache-Control: no-cache, must-revalidate');
        header("Expires: 0");
        header("Last-Modified: " . gmdate('D, d M Y H:i:s', $this->lastModified) . ' GMT');
        $streamStartPoint = 0;
        $streamEndPoint   = $this->fileSize - 1;
        // set the range in the header
        header("Accept-Ranges: 0-" . $streamEndPoint);
        if (isset($_SERVER['HTTP_RANGE'])) {
            $currentStreamStart = $streamStartPoint;
            $currentStreamEnd   = $streamEndPoint;

            list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
            if (strpos($range, ',') !== false) {
                header('HTTP/1.1 416 Requested Range Not Satisfiable');
                header("Content-Range: bytes " . $streamStartPoint . "-" . $streamEndPoint . "/" . $this->fileSize);
                exit;
            }
            if ($range == '-') {
                $currentStreamStart = $this->fileSize - substr($range, 1);
            } else {
                $range              = explode('-', $range);
                $currentStreamStart = $range[0];
                $currentStreamEnd   = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $currentStreamEnd;
            }
            $currentStreamEnd = ($currentStreamEnd > $streamEndPoint) ? $streamEndPoint : $currentStreamEnd;
            if ($currentStreamStart > $currentStreamEnd || $currentStreamStart > $this->fileSize - 1 || $currentStreamEnd >= $this->fileSize) {
                header('HTTP/1.1 416 Requested Range Not Satisfiable');
                header("Content-Range: bytes " . $streamStartPoint . "-" . $streamEndPoint . "/" . $this->fileSize);
                exit;
            }
            $streamStartPoint = $currentStreamStart;
            $streamEndPoint   = $currentStreamEnd;
            $length           = $streamEndPoint - $streamStartPoint + 1;
            fseek($this->video, $streamStartPoint);
            header('HTTP/1.1 206 Partial Content');
            header("Content-Length: " . $length);
            header("Content-Range: bytes " . $streamStartPoint . "-" . $streamEndPoint . "/" . $this->fileSize);
        } else {
            header("Content-Length: " . $this->fileSize);
        }

        $endPointer = $streamStartPoint;
        set_time_limit(0);
        while (!feof($this->video) && $endPointer <= $streamEndPoint) {
            $bytesToRead = self::BUFFER;
            if (($endPointer + $bytesToRead) > $streamEndPoint) {
                $bytesToRead = $streamEndPoint - $endPointer + 1;
            }
            $data = fread($this->video, $bytesToRead);
            echo $data;
            flush();
            $endPointer += $bytesToRead;
        }
        fclose($this->video);
    }
}
