<?php


require_once __DIR__ . '/../config/Database.php';

class Contact
{
    private PDO $db;
    private string $table = 'contacts';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // ── READ ──────────────────────────────────────────────────────────────

    public function getAll(string $sort = 'nom_asc', int $page = 1, int $perPage = 8): array
    {
        $orderMap = [
            'nom_asc'  => 'nom ASC, prenom ASC',
            'nom_desc' => 'nom DESC, prenom DESC',
            'recent'   => 'created_at DESC',
            'ancien'   => 'created_at ASC',
        ];
        $order  = $orderMap[$sort] ?? 'nom ASC';
        $offset = ($page - 1) * $perPage;

        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} ORDER BY {$order} LIMIT :limit OFFSET :offset"
        );
        $stmt->bindValue(':limit',  $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset,  PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countAll(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM {$this->table}")->fetchColumn();
    }

    public function getById(int $id): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function search(string $query): array
    {
        $like = '%' . $query . '%';
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table}
             WHERE nom LIKE :q1 OR prenom LIKE :q2 OR email LIKE :q3 OR telephone LIKE :q4
             ORDER BY nom ASC
             LIMIT 50"
        );
        $stmt->execute([
            ':q1' => $like,
            ':q2' => $like,
            ':q3' => $like,
            ':q4' => $like,
        ]);
        return $stmt->fetchAll();
    }

    // ── CREATE ────────────────────────────────────────────────────────────

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO {$this->table} (nom, prenom, telephone, email, photo)
             VALUES (:nom, :prenom, :telephone, :email, :photo)"
        );
        $stmt->execute([
            ':nom'       => $this->sanitize($data['nom']),
            ':prenom'    => $this->sanitize($data['prenom']),
            ':telephone' => $this->sanitize($data['telephone']),
            ':email'     => $this->sanitize($data['email']),
            ':photo'     => $data['photo'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    // ── UPDATE ────────────────────────────────────────────────────────────

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE {$this->table}
             SET nom = :nom, prenom = :prenom, telephone = :telephone,
                 email = :email, photo = :photo
             WHERE id = :id"
        );
        return $stmt->execute([
            ':nom'       => $this->sanitize($data['nom']),
            ':prenom'    => $this->sanitize($data['prenom']),
            ':telephone' => $this->sanitize($data['telephone']),
            ':email'     => $this->sanitize($data['email']),
            ':photo'     => $data['photo'] ?? null,
            ':id'        => $id,
        ]);
    }

    // ── DELETE ────────────────────────────────────────────────────────────

    public function delete(int $id): bool
    {
        $contact = $this->getById($id);
        if ($contact && $contact['photo']) {
            $path = __DIR__ . '/../../public/' . $contact['photo'];
            if (file_exists($path)) {
                @unlink($path);
            }
        }
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    // ── HELPERS ───────────────────────────────────────────────────────────

    private function sanitize(string $value): string
    {
        return htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
    }
}
