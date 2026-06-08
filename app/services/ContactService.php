<?php
// app/services/ContactService.php

require_once __DIR__ . '/../models/Contact.php';
require_once __DIR__ . '/FileUploadService.php';

class ContactService
{
    public function __construct(
        private Contact           $model,
        private FileUploadService $uploader,
        private string            $publicRoot,
    ) {}

    // ── Queries ───────────────────────────────────────────────────────────

    public function paginate(string $sort, int $page, int $perPage = 8): array
    {
        $total = $this->model->countAll();
        $pages = (int) ceil($total / $perPage);
        $page  = min(max(1, $page), max(1, $pages));

        return [
            'contacts' => $this->model->getAll($sort, $page, $perPage),
            'page'     => $page,
            'pages'    => $pages,
            'total'    => $total,
        ];
    }

    public function findOrFail(int $id): array
    {
        $contact = $this->model->getById($id);

        if (!$contact) {
            throw new \RuntimeException("Contact #{$id} introuvable.", 404);
        }

        return $contact;
    }

    public function search(string $query): array
    {
        if (trim($query) === '') {
            return [];
        }

        return array_map(
            fn($c) => array_map(fn($v) => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'), $c),
            $this->model->search($query)
        );
    }

    public function all(): array
    {
        return $this->model->getAll('nom_asc', 1, 9999);
    }

    // ── Mutations ─────────────────────────────────────────────────────────

    /**
     * @throws \InvalidArgumentException  on validation failure (carries field errors)
     * @throws \RuntimeException          on upload or DB failure
     */
    public function create(array $data, ?array $file = null): void
    {
        $this->assertValid($data);

        $this->model->create(array_merge(
            $this->sanitize($data),
            ['photo' => $file ? $this->uploader->store($file) : null]
        ));
    }

    /**
     * @throws \InvalidArgumentException  on validation failure
     * @throws \RuntimeException          on upload or DB failure
     */
    public function update(int $id, array $data, ?array $file = null): void
    {
        $contact = $this->findOrFail($id);
        $this->assertValid($data);

        $photo = $contact['photo'];

        if ($file) {
            $newPhoto = $this->uploader->store($file);

            if ($photo) {
                $this->uploader->delete($photo, $this->publicRoot);
            }

            $photo = $newPhoto;
        }

        $this->model->update($id, array_merge(
            $this->sanitize($data),
            ['photo' => $photo]
        ));
    }

    public function delete(int $id): void
    {
        if (!$this->model->delete($id)) {
            throw new \RuntimeException('Suppression échouée.');
        }
    }

    // ── Validation ────────────────────────────────────────────────────────

    /**
     * @throws \InvalidArgumentException  carrying ['field' => 'message', ...] as message (JSON)
     */
    private function assertValid(array $data): void
    {
        $errors = [];

        $nom = trim($data['nom'] ?? '');
        if ($nom === '')           $errors['nom'] = 'Le nom est obligatoire.';
        elseif (mb_strlen($nom) > 50) $errors['nom'] = 'Le nom ne doit pas dépasser 50 caractères.';

        $prenom = trim($data['prenom'] ?? '');
        if ($prenom === '')              $errors['prenom'] = 'Le prénom est obligatoire.';
        elseif (mb_strlen($prenom) > 50) $errors['prenom'] = 'Le prénom ne doit pas dépasser 50 caractères.';

        $tel = preg_replace('/\s+/', '', $data['telephone'] ?? '');
        if ($tel === '')                             $errors['telephone'] = 'Le téléphone est obligatoire.';
        elseif (!preg_match('/^\+?[0-9]{8,15}$/', $tel)) $errors['telephone'] = 'Numéro invalide (8-15 chiffres).';

        $email = trim($data['email'] ?? '');
        if ($email === '')                                    $errors['email'] = "L'email est obligatoire.";
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Adresse email invalide.';

        if (!empty($errors)) {
            $ex = new \InvalidArgumentException('Validation failed.');
            $ex->errors = $errors; // attach bag directly
            throw $ex;
        }
    }

    private function sanitize(array $data): array
    {
        return [
            'nom'       => trim($data['nom']),
            'prenom'    => trim($data['prenom']),
            'telephone' => preg_replace('/\s+/', '', $data['telephone']),
            'email'     => trim($data['email']),
        ];
    }
}