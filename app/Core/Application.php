<?php

namespace App\Core;

class Application
{
    public static string $ROOT_DIR;
    public Router $router;
    public Request $request;
    public Response $response;
    public Session $session;
    public Database $db;
    public ?Controller $controller = null;

    public function __construct($rootPath)
    {
        self::$ROOT_DIR = $rootPath;
        $this->request = new Request();
        $this->response = new Response();
        $this->session = new Session();
        $this->router = new Router();
        $this->db = Database::getInstance();
    }

    public function run()
    {
        try {
            $this->router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
        } catch (\Exception $e) {
            $this->response->setStatusCode($e->getCode() ?: 500);
            $this->render('_error', [
                'exception' => $e
            ]);
        }
    }

    protected function render(string $view, array $data = []): void
    {
        extract($data);
        require_once __DIR__ . "/../Views/{$view}.php";
    }
}
