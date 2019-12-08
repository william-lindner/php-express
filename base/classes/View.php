<?php

namespace Express;

final class View
{
    protected static $instance = null;

    protected $data = [];

    public function __construct(array $data = [])
    {
        if (self::$instance) {
            return self::$instance;
        }

        $this->data = array_merge($this->data, $data);

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
        require $view_path . $file;
        return ob_get_clean();
    }

    public function component(?string $component = null, array $data = [])
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

    public static function resource(string $type, string $path, bool $absolute = false)
    {
    }

    protected function scripts()
    {
        //
    }

    public function __get($key)
    {
        return $this->data[$key] ?? null;
    }
}
