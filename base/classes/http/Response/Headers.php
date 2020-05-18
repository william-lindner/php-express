<?php


namespace Express\Http\Response;


class Headers
{
    public const CONTENT_HTML = 'html';

    protected const DEFAULT_TYPE = 'html';

    private const CONTENT_TYPES = [
        'json'      => 'application/json',
        'text'      => 'text/html',
        'html'      => 'text/html',
        'form-data' => 'multipart/form-data',
        'xml'       => 'application/xml'
    ];
    private const HEADER_PROPS = [
        'charset'     => 1,
        'contentType' => 1
    ];

    private $header;

    private $code = 200;

    public function type(string $type)
    {
        $content = self::CONTENT_TYPES[$type];

        header("Content-Type: $content;charset=utf-8");
    }

    public function code()
    {
        //
    }

    /**
     * Output the headers list
     *
     * @return array
     */
    public function list() : array
    {
        return headers_list();
    }

    protected function generate() : void
    {
        header($this->header, true, $this->code);
        //        header("$protocol $code $reason", true, $code);
    }

}