<?php
namespace App\Core;

class Controller
{
    protected View $view;
    protected Database $db;

    public function __construct()
    {
        $this->view = new View();
        $this->db = Database::getInstance();
    }

    protected function render(string $template, array $data = [], string $layout = 'app'): void
    {
        $this->view->render($template, $data, $layout);
    }

    protected function renderAdmin(string $template, array $data = []): void
    {
        $this->view->render($template, $data, 'admin');
    }

    protected function json(array $data, int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function back(): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        redirect($referer);
    }

    protected function validate(array $rules): array
    {
        $validator = new Validator($_POST, $rules);
        if (!$validator->passes()) {
            flash('errors', $validator->errors());
            flash('old', $_POST);
            $this->back();
        }
        return $validator->validated();
    }
}
