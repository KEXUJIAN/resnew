<?php

/**
* Customize Controller
*/
class MY_Controller extends CI_Controller
{
    public function view(string $name, array $params)
    {
        $path = realpath(VIEWPATH . "{$name}.php");
        if (!$path) {
            show_404();
        }
        $params['CI'] =& get_instance();
        $this->load->view($name, $params);
    }

    public function includeJs($name)
    {
        $jsPath = realpath(ROOT_PATH . "assets/dest/{$name}");
        $file = "/assets/{$name}";
        if (!$jsPath) {
            echo '<script type="text/javascript">"' . $name . ' is not exists"</script>';
            return;
        }
        echo '<script type="text/javascript" src="' . $file . '"></script>';
    }
}