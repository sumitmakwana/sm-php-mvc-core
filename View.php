<?php

namespace app\core;

class View
{
    public string $title = "";

    public function renderView($view, $params = [])
    {
        $viewContent = $this->renderOnlyView($view,$params);
        $layoutsContent = $this->layoutContent();
        return str_replace("{{content}}",$viewContent,$layoutsContent);
    }

    public function renderContent($viewContent)
    {
        $layoutsContent = $this->layoutContent();
        return str_replace("{{content}}",$viewContent,$layoutsContent);
    }

    protected function layoutContent()
    {
        $layout = Application::$app->layout;
        if(Application::$app->controller) {
            $layout = Application::$app->controller->layout;
        }
        ob_start();
        include_once Application::$ROOT_DIR."/views/layouts/$layout.php";
        return ob_get_clean();
    }

    protected function renderOnlyView($view, $params)
    {
        foreach ($params as $key=>$value)
        {
            $$key = $value;
        }
//        var_dump($name);
        ob_start();
        include_once Application::$ROOT_DIR."/views/$view.php";
        return ob_get_clean();
    }
}