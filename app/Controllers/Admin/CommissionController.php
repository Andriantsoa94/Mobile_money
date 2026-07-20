<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CommissionModel;
use App\Models\OperateurModel;

class CommissionController extends BaseController
{
    public function index()
    {
        $commissions = (new CommissionModel())->listeAvecOperateur();

        return view('admin/commissions', [
            'commissions' => $commissions,
        ]);
    }

    public function nouveau()
    {
        return view('admin/commissionForm', [
            'commission' => null,
            'operateurs' => (new OperateurModel())->orderBy('nom', 'ASC')->findAll(),
        ]);
    }

    public function creer()
    {
        $donnees = $this->donneesFormulaire();

        $erreur = $this->valider($donnees);
        if ($erreur !== null) {
            return redirect()->back()->withInput()->with('error', $erreur);
        }

        (new CommissionModel())->insert($donnees);

        return redirect()->to('/admin/commissions')->with('success', 'Commission créée.');
    }

    public function modifier(int $id)
    {
        $commission = (new CommissionModel())->find($id);
        if (! $commission) {
            return redirect()->to('/admin/commissions')->with('error', 'Commission introuvable.');
        }

        return view('admin/commissionForm', [
            'commission' => $commission,
            'operateurs' => (new OperateurModel())->orderBy('nom', 'ASC')->findAll(),
        ]);
    }

    public function mettreAJour(int $id)
    {
        $donnees = $this->donneesFormulaire();

        $erreur = $this->valider($donnees);
        if ($erreur !== null) {
            return redirect()->back()->withInput()->with('error', $erreur);
        }

        (new CommissionModel())->update($id, $donnees);

        return redirect()->to('/admin/commissions')->with('success', 'Commission mise à jour.');
    }

    public function supprimer(int $id)
    {
        (new CommissionModel())->delete($id);
        return redirect()->to('/admin/commissions')->with('success', 'Commission supprimée.');
    }

    private function donneesFormulaire(): array
    {
        return [
            'idOperateur' => (int) $this->request->getPost('idOperateur') ?: null,
            'commission'  => (float) $this->request->getPost('commission'),
            'pourcentage' => (float) $this->request->getPost('pourcentage'),
        ];
    }

    private function valider(array $donnees): ?string
    {
        if (empty($donnees['idOperateur'])) {
            return 'Veuillez sélectionner un opérateur.';
        }
        if ($donnees['commission'] < 0 || $donnees['pourcentage'] < 0) {
            return 'La commission et le pourcentage ne peuvent pas être négatifs.';
        }
        if ($donnees['pourcentage'] > 100) {
            return 'Le pourcentage ne peut pas dépasser 100.';
        }
        return null;
    }
}
