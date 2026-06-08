<?php
// app/controllers/ContactController.php

require_once __DIR__ . '/../helpers/CsrfHelper.php';
require_once __DIR__ . '/../services/ContactService.php';
require_once __DIR__ . '/../services/FileUploadService.php';
require_once __DIR__ . '/../services/PdfExportService.php';
require_once __DIR__ . '/../models/Contact.php';

class ContactController
{
    private ContactService   $service;
    private PdfExportService $pdf;

    public function __construct()
    {
        $uploadDir  = __DIR__ . '/../../public/uploads/photos/';
        $publicRoot = __DIR__ . '/../../public/';

        $this->service = new ContactService(
            model:      new Contact(),
            uploader:   new FileUploadService($uploadDir),
            publicRoot: $publicRoot,
        );

        $this->pdf = new PdfExportService($publicRoot);
    }

    // ── INDEX ─────────────────────────────────────────────────────────────

    public function index(): void
    {
        $sort = $_GET['sort'] ?? 'nom_asc';
        $page = max(1, (int)($_GET['page'] ?? 1));

        ['contacts' => $contacts, 'page' => $page, 'pages' => $pages, 'total' => $total]
            = $this->service->paginate($sort, $page);

        require_once __DIR__ . '/../../views/contacts/index.php';
    }

    // ── CREATE ────────────────────────────────────────────────────────────

    public function create(): void
    {
        $errors  = [];
        $old     = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                CsrfHelper::validateOrFail($_POST['csrf_token'] ?? '');
                $file = $this->resolveFile('photo');
                $this->service->create($_POST, $file);
                $this->redirectWith('created');
            } catch (\RuntimeException $e) {
                $errors[] = $e->getMessage();
                $old = $_POST;
            } catch (\InvalidArgumentException $e) {
                $errors = $e->errors ?? [$e->getMessage()];
                $old = $_POST;
            }
        }

        require_once __DIR__ . '/../../views/contacts/create.php';
    }

    // ── EDIT ──────────────────────────────────────────────────────────────

    public function edit(): void
    {
        $id = (int)($_GET['id'] ?? 0);

        try {
            $contact = $this->service->findOrFail($id);
        } catch (\RuntimeException $e) {
            http_response_code(404);
            require_once __DIR__ . '/../../views/errors/404.php';
            exit;
        }

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                CsrfHelper::validateOrFail($_POST['csrf_token'] ?? '');
                $file = $this->resolveFile('photo');
                $this->service->update($id, $_POST, $file);
                $this->redirectWith('updated');
            } catch (\RuntimeException $e) {
                $errors[] = $e->getMessage();
            } catch (\InvalidArgumentException $e) {
                $errors = $e->errors ?? [$e->getMessage()];
            }
        }

        require_once __DIR__ . '/../../views/contacts/edit.php';
    }

    // ── DELETE ────────────────────────────────────────────────────────────

    public function delete(): void
    {
        header('Content-Type: application/json');

        try {
            CsrfHelper::validateOrFail($_POST['csrf_token'] ?? '');
            $this->service->delete((int)($_POST['id'] ?? 0));
            echo json_encode(['success' => true]);
        } catch (\RuntimeException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }

        exit;
    }

    // ── SEARCH ────────────────────────────────────────────────────────────

    public function search(): void
    {
        header('Content-Type: application/json');

        $results = $this->service->search($_GET['q'] ?? '');
        echo json_encode(['results' => $results, 'count' => count($results)]);

        exit;
    }

    // ── EXPORT PDF ────────────────────────────────────────────────────────

    public function exportPdf(): void
    {
        $this->pdf->stream($this->service->all());
    }

    // ── Private helpers ───────────────────────────────────────────────────

    /** Returns the uploaded file array only when a real file was submitted. */
    private function resolveFile(string $field): ?array
    {
        $file = $_FILES[$field] ?? null;

        return ($file && !empty($file['name'])) ? $file : null;
    }

    private function redirectWith(string $flag): never
    {
        header("Location: index.php?success={$flag}");
        exit;
    }
}