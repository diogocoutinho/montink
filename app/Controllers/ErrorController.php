<?php

namespace App\Controllers;

use App\Core\Controller;

class ErrorController extends Controller
{
    public function notFound(): void
    {
        http_response_code(404);
        $this->render('errors/404');
    }

    public function serverError(): void
    {
        http_response_code(500);
        $this->render('errors/500');
    }

    public function forbidden(): void
    {
        http_response_code(403);
        $this->render('errors/403');
    }
}
