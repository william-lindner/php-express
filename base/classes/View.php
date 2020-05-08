<?php

namespace Express;

final class View
{
    protected $data = [];

    public function __construct(array $data = [])
    {
        $this->data = $data;

        Container::store('view', $this);
    }

    public static function resource(string $type, string $path, bool $absolute = false)
    {
        //
    }

    public function render(string $view = null)
    {
        $view_path = __VIEWDIR__ . '/';
        $file = str_replace('.', '/', $view ?? $this->view) . '.view.php';

        if (!file_exists($view_path . $file)) {
            throw new \RuntimeException('Unable to locate view: ' . $view_path . $file, 400);
        }

        ob_start();
        require $view_path . $file;
        return ob_get_clean();
    }

    public function __get($key)
    {
        return $this->data[$key] ?? null;
    }

    protected function scripts()
    {
        //
    }
}
