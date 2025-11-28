<?php
class BaseController {
    
    protected function view($view, $data = []) {
        // Extract data array menjadi variables
        extract($data);
        
        // Include view file
        $viewFile = __DIR__ . '/../views/' . $view . '.php';
        
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            die("View file not found: " . $viewFile);
        }
    }
    
    protected function redirect($url) {
        header('Location: ' . $url);
        exit;
    }
}
?>