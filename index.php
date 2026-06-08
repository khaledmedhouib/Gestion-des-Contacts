<?php
// index.php – Front Controller

session_start();

define('ROOT', __DIR__);
define('APP',  ROOT . '/app');

require_once APP . '/controllers/ContactController.php';

// Generate CSRF token once per session
CsrfHelper::generate();

$action = $_GET['action'] ?? 'index';

$controller = new ContactController();

switch ($action) {
    case 'index':   $controller->index();     break;
    case 'create':  $controller->create();    break;
    case 'edit':    $controller->edit();      break;
    case 'delete':  $controller->delete();    break;
    case 'search':  $controller->search();    break;
    case 'pdf':     $controller->exportPdf(); break;
    default:
        http_response_code(404);
        require_once ROOT . '/views/errors/404.php';
}
