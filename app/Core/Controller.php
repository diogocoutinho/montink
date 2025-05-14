<?php

namespace App\Core;

class Controller
{
    protected function render(string $view, array $data = []): void
    {
        // Extrair dados para variáveis
        extract($data);

        // Iniciar buffer de saída
        ob_start();

        // Incluir a view
        $viewPath = __DIR__ . '/../Views/' . $view . '.php';
        if (!file_exists($viewPath)) {
            throw new \Exception("View not found: {$view}");
        }
        require $viewPath;

        // Capturar o conteúdo da view
        $content = ob_get_clean();

        // Incluir o layout
        require __DIR__ . '/../Views/layouts/main.php';
    }

    protected function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }

    protected function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function isGet(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    protected function getPostData(): array
    {
        return $_POST;
    }

    protected function getQueryParams(): array
    {
        return $_GET;
    }

    protected function setFlashMessage(string $type, string $message): void
    {
        $_SESSION[$type] = $message;
    }

    protected function getFlashMessage(string $type): ?string
    {
        if (isset($_SESSION[$type])) {
            $message = $_SESSION[$type];
            unset($_SESSION[$type]);
            return $message;
        }
        return null;
    }

    protected function getStatusBadgeClass(string $status): string
    {
        return match ($status) {
            'pending' => 'warning',
            'processing' => 'info',
            'shipped' => 'primary',
            'delivered' => 'success',
            'cancelled' => 'danger',
            default => 'secondary'
        };
    }
}
