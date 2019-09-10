<?php

namespace Express;

final class View
{
    protected static $instance = null;

    protected $data = [];

    public function __construct(?string $view = null, array $data = [])
    {
        if (self::$instance) {
            return self::$instance;
        }

        $this->data            = $data;

        if ($view) {
            $this->view = $view;
        }

        return self::$instance = $this;
    }

    public function render(string $view = null)
    {
        $view_path = __VIEWDIR__ . '/';
        $file      = str_replace('.', '/', $view ?? $this->view) . '.view.php';

        if (!file_exists($view_path . $file)) {
            throw new \Exception('Unable to locate view: ' . $view_path . $file, 400);
        }

        ob_start();
        require_once $view_path . $file;
        return ob_get_clean();
    }

    public function component(?string $component = null, array $data = [])
    {
        //
    }

    public function load(string $viewPath)
    {
        //
    }

    public function add()
    {
        //
    }

    public function extract()
    {
        //
    }

    public function __get($key)
    {
        return $this->data[$key] ?? null;
    }
}
