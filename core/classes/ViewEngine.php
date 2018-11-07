<?php

namespace Teapot;

abstract class ViewEngine
{
    protected $viewPath;
    protected $widgetPath;

    private $viewContents;
    private $view;

    public function __construct($viewPath, $relative = true)
    {
        // setup the location for the views / templates
        if ($relative) {
            $this->viewPath = $_SERVER['DOCUMENT_ROOT'] . $viewPath;
        } else {
            $this->viewPath = $viewPath;
        }
        $this->widgetPath = $_SERVER['DOCUMENT_ROOT'] . '/widgets/';
    }

    public function loadView($view, $data = [])
    {
        $fullViewPath = $this->viewPath . $view;

        // check to see if the file is actually there
        if (!file_exists($fullViewPath)) {
            throw new \Exception('Unable to locate view file.', 404); # https://www.w3schools.com/tags/ref_httpmessages.asp
        }

        $this->viewContents = @file_get_contents($fullViewPath);
        $this->view         = $this->replaceTokens($data);
        echo $this->view;
    }

    final private function replaceTokens($data = [])
    {
        // check if tokens exist and that data has been passed
        $tokensExist = preg_match('/({{[^}]*}})/', $this->viewContents);
        if (!$data || !$tokensExist) {
            return $this->viewContents;
        }
        $viewMinusTokens = preg_split('/({{[^}]*}})/', $this->viewContents);
        preg_match_all('/({{[^}]*}})/', $this->viewContents, $viewTokens);
        $viewTokens = $viewTokens[0];
        // loop through tokens for things to replace
        for ($i = 0; $i < count($viewTokens); $i++) {
            $viewToken = &$viewTokens[$i];
            if (strpos($viewToken, '::')) {
                // When function :: dataset syntax is used
                // find the function in the class.
                $functionArray = explode('::', $viewToken);
                $function      = substr($functionArray[0], 2);
                $dataKey       = substr($functionArray[1], 0, -2);
                if (!method_exists($this, $function)) {
                    // needs to throw exception
                    echo 'No method to handle data reference<br>';
                }
                if (!isset($data[$dataKey])) {
                    // needs exception
                    echo 'Data point does not exist';
                }
                // load the method - passing the data
                $viewToken = $this->{$function}($data[$dataKey]);
            } elseif (strpos($viewToken, '>')) {
                // widget loader
            } else {
                $token = substr($viewToken, 2, -2);
                if (!isset($data[$token])) {
                    echo 'Woops...<br>';
                    //throw new \Exception("Not all tokens replaced.", 206); # https://www.w3schools.com/tags/ref_httpmessages.asp
                }
                $viewToken = $data[$token];
            }
        }

        $compiledView = '';
        // reassemble the view for export
        for ($i = 0; $i < count($viewMinusTokens); $i++) {
            $compiledView .= $viewMinusTokens[$i] . $viewTokens[$i];
        }
        return $compiledView;
    }

    public function createWidget($object)
    {

    }
}

/*

{{standard_token}}

{{>widget_token}}

{{method::key}}
 */
