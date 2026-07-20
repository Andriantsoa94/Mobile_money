<?php

namespace App\Models;

use CodeIgniter\Model;

class LivreModel extends Model
{
    protected $table = 'livres';

    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType = 'array';

    protected $protectFields = true;

    protected $allowedFields = [
        'titre',
        'auteur',
        'isbn',
        'annee_publication',
        'categorie',
        'resume',
        'nom_fichier_couverture',
        'statut',
    ];

    protected $useTimestamps = true;

    protected $createdField = 'created_at';

    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'id' => 'permit_empty|is_natural_no_zero',
        'titre' => 'required|min_length[3]|max_length[255]',
        'auteur' => 'required|max_length[255]',
        'isbn' => 'required|max_length[32]|is_unique[livres.isbn,id,{id}]',
        'annee_publication' => 'required|regex_match[/^\d{4}$/]',
        'categorie' => 'permit_empty|max_length[120]',
        'resume' => 'permit_empty',
        'nom_fichier_couverture' => 'permit_empty|max_length[255]',
        'statut' => 'permit_empty|in_list[disponible,prete]',
    ];

    protected $validationMessages = [
        'titre' => [
            'required' => 'Le titre est obligatoire.',
            'min_length' => 'Le titre doit contenir au moins 3 caracteres.',
        ],
        'auteur' => [
            'required' => 'L\'auteur est obligatoire.',
        ],
        'isbn' => [
            'required' => 'L\'ISBN est obligatoire.',
            'is_unique' => 'Cet ISBN existe deja dans la base de donnees.',
        ],
        'annee_publication' => [
            'required' => 'L\'annee de publication est obligatoire.',
            'regex_match' => 'L\'annee de publication doit etre au format YYYY.',
        ],
    ];

    private array $businessErrors = [];

    public function getAllLivres(): array
    {
        return $this->orderBy('created_at', 'DESC')->findAll();
    }

    public function getLivresPagines(int $perPage = 10, string $group = 'default', ?string $sort = null, ?string $dir = null): array
    {
        [$sortColumn, $sortDirection] = $this->resolveSort($sort, $dir);

        return $this->orderBy($sortColumn, $sortDirection)->paginate($perPage, $group);
    }

    public function getLivreById(int $id): ?array
    {
        return $this->find($id);
    }

    public function createLivre(array $data): bool
    {
        $this->businessErrors = [];
        $data = $this->normalizeLivreData($data);

        if (! $this->validateAnneePublicationMetier($data)) {
            return false;
        }

        return $this->insert($data) !== false;
    }

    public function updateLivre(int $id, array $data): bool
    {
        $this->businessErrors = [];
        $data = $this->normalizeLivreData($data);
        $data['id'] = $id;

        if (! $this->validateAnneePublicationMetier($data)) {
            return false;
        }

        return $this->update($id, $data);
    }

    public function deleteLivre(int $id): bool
    {
        return $this->delete($id);
    }

    public function updateStatut(int $id, string $statut): bool
    {
        return $this->update($id, ['statut' => $statut]);
    }

    public function isbnExists(string $isbn, ?int $ignoreId = null): bool
    {
        $builder = $this->builder();
        $builder->where('isbn', $isbn);

        if ($ignoreId !== null) {
            $builder->where('id !=', $ignoreId);
        }

        return (int) $builder->countAllResults() > 0;
    }

    public function isAnneePublicationValide(int $annee): bool
    {
        return $annee <= (int) date('Y');
    }

    public function rechercherLivres(?string $motCle = null, ?string $categorie = null, ?string $sort = null, ?string $dir = null): array
    {
        $motCle = trim((string) $motCle);
        $categorie = trim((string) $categorie);
        [$sortColumn, $sortDirection] = $this->resolveSort($sort, $dir);

        $builder = $this->builder();

        if ($motCle !== '') {
            $builder->groupStart()
                ->like('titre', $motCle)
                ->orLike('auteur', $motCle)
                ->groupEnd();
        }

        if ($categorie !== '') {
            $builder->where('categorie', $categorie);
        }

        return $builder
            ->orderBy($sortColumn, $sortDirection)
            ->get()
            ->getResultArray();
    }

    public function syncAuteursForLivre(int $livreId, string $auteursTexte): void
    {
        $auteurs = $this->parseAuteursInput($auteursTexte);
        $pivot = $this->db->table('livre_auteur');

        $pivot->where('livre_id', $livreId)->delete();

        if ($auteurs === []) {
            return;
        }

        $auteurModel = new AuteurModel();

        foreach ($auteurs as $nomAuteur) {
            $auteurId = $auteurModel->findOrCreateByName($nomAuteur);

            $pivot->insert([
                'livre_id' => $livreId,
                'auteur_id' => $auteurId,
            ]);
        }
    }

    public function getAuteursTexteByLivreId(int $livreId): string
    {
        $rows = $this->db->table('livre_auteur')
            ->select('auteurs.nom')
            ->join('auteurs', 'auteurs.id = livre_auteur.auteur_id', 'inner')
            ->where('livre_auteur.livre_id', $livreId)
            ->orderBy('auteurs.nom', 'ASC')
            ->get()
            ->getResultArray();

        if ($rows === []) {
            return '';
        }

        $noms = array_map(static fn (array $row): string => (string) $row['nom'], $rows);

        return implode(', ', $noms);
    }

    /**
     * @param list<array<string, mixed>> $livres
     * @return list<array<string, mixed>>
     */
    public function enrichLivresWithAuteurs(array $livres): array
    {
        if ($livres === []) {
            return $livres;
        }

        $ids = array_map(static fn (array $livre): int => (int) ($livre['id'] ?? 0), $livres);
        $ids = array_values(array_filter($ids, static fn (int $id): bool => $id > 0));

        if ($ids === []) {
            return $livres;
        }

        $rows = $this->db->table('livre_auteur')
            ->select('livre_auteur.livre_id, auteurs.nom')
            ->join('auteurs', 'auteurs.id = livre_auteur.auteur_id', 'inner')
            ->whereIn('livre_auteur.livre_id', $ids)
            ->orderBy('auteurs.nom', 'ASC')
            ->get()
            ->getResultArray();

        $map = [];

        foreach ($rows as $row) {
            $livreId = (int) $row['livre_id'];
            $map[$livreId] ??= [];
            $map[$livreId][] = (string) $row['nom'];
        }

        foreach ($livres as &$livre) {
            $livreId = (int) ($livre['id'] ?? 0);
            $auteursTexte = isset($map[$livreId]) ? implode(', ', array_unique($map[$livreId])) : '';
            $livre['auteur_affichage'] = $auteursTexte !== '' ? $auteursTexte : (string) ($livre['auteur'] ?? '');
        }
        unset($livre);

        return $livres;
    }

    public function getCategoriesDisponibles(): array
    {
        $result = $this->builder()
            ->select('categorie')
            ->where('categorie IS NOT NULL', null, false)
            ->where('categorie !=', '')
            ->groupBy('categorie')
            ->orderBy('categorie', 'ASC')
            ->get()
            ->getResultArray();

        return array_map(static fn (array $row): string => (string) $row['categorie'], $result);
    }

    public function getBusinessErrors(): array
    {
        return $this->businessErrors;
    }

    private function validateAnneePublicationMetier(array $data): bool
    {
        $annee = $data['annee_publication'] ?? null;

        if ($annee === null || $annee === '') {
            return true;
        }

        if (! ctype_digit((string) $annee)) {
            return true;
        }

        if (! $this->isAnneePublicationValide((int) $annee)) {
            $this->businessErrors = [
                'annee_publication' => 'L\'annee de publication ne peut pas etre dans le futur.',
            ];

            return false;
        }

        return true;
    }

    private function normalizeLivreData(array $data): array
    {
        if (array_key_exists('annee_publication', $data)) {
            $value = trim((string) $data['annee_publication']);
            $data['annee_publication'] = $value === '' ? null : $value;
        }

        return $data;
    }

    /**
     * @return array{0:string,1:string}
     */
    private function resolveSort(?string $sort, ?string $dir): array
    {
        $map = [
            'titre' => 'titre',
            'auteur' => 'auteur',
            'annee' => 'annee_publication',
        ];

        $sortKey = strtolower(trim((string) $sort));
        $direction = strtolower(trim((string) $dir)) === 'desc' ? 'DESC' : 'ASC';

        if (! isset($map[$sortKey])) {
            return ['created_at', 'DESC'];
        }

        return [$map[$sortKey], $direction];
    }

    /**
     * @return list<string>
     */
    private function parseAuteursInput(string $input): array
    {
        $parts = array_map('trim', explode(',', $input));
        $parts = array_filter($parts, static fn (string $value): bool => $value !== '');

        return array_values(array_unique($parts));
    }
}