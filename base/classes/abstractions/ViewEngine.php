<?php

namespace Express\Abstractions;

abstract class ViewEngine
{
    protected $viewPath;
    private $viewContents;

    public function __construct($viewPath, $relativePath = true)
    {
        if ($relativePath) {
            $this->viewPath = $_SERVER['DOCUMENT_ROOT'] . $viewPath;
        } else {
            $this->viewPath = $viewPath;
        }
    }

    // NOTE: The loadView is left in a state that allows it to be overridden intentionally.
    public function loadView($view, $data = [])
    {
        $fullViewPath = $this->viewPath . $view;
        if (!file_exists($fullViewPath)) {
            # https://www.w3schools.com/tags/ref_httpmessages.asp
            throw new \Exception('Unable to locate view file at location: ' . $fullViewPath, 404);
        }

        $this->viewContents = @file_get_contents($fullViewPath);
        if (!$this->viewContents) {
            throw new \Exception('File found but unable to be loaded. Please verify permissions.', 409);
        }
        echo $this->replaceTokens($data);
    }

    final private function replaceTokens($data = [])
    {
        // TODO: Line 65 and 70 are a repeat. These can be merged to reduce time complexity
        $tokensExist = preg_match('/({{[^}]*}})/', $this->viewContents);
        if (!$data || !$tokensExist) {
            return $this->viewContents;
        }

        $viewMinusTokens = preg_split('/({{[^}]*}})/', $this->viewContents);
        preg_match_all('/({{[^}]*}})/', $this->viewContents, $viewTokens);
        $viewTokens = $viewTokens[0];

        for ($i = 0; $i < count($viewTokens); $i++) {
            $viewToken = &$viewTokens[$i];

            if (strpos($viewToken, '::')) {
                $functionArray = explode('::', $viewToken);
                $function = substr($functionArray[0], 2);
                $dataKey = substr($functionArray[1], 0, -2);

                if (!method_exists($this, $function)) {
                    throw new \Exception('No Method exists to handle the template token.', 409);
                }
                if (!isset($data[$dataKey])) {
                    throw new \Exception('Data point does not exist : ' . $dataKey, 400);
                }

                $viewToken = $this->{$function}($data[$dataKey]);
                continue;
            }

            $token = substr($viewToken, 2, -2);
            if (!isset($data[$token])) {
                throw new \Exception('Token does not exist : ' . $token, 400);
            }
            $viewToken = $data[$token];
        }

        if (count($viewMinusTokens) !== (count($viewTokens) + 1)) {
            throw new Exception('Not all tokens replaced.', 201);
        }

        $compiledView = '';
        for ($i = 0; $i < count($viewTokens); $i++) {
            $compiledView .= $viewMinusTokens[$i] . $viewTokens[$i];
        }
        $compiledView .= array_pop($viewMinusTokens);
        return $compiledView;
    }
}
