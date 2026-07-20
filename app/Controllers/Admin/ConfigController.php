<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ConfigModel;

class ConfigController extends BaseController
{
    public function index()
    {
        $baremes = (new ConfigModel())->listeTriee();

        return view('admin/configBareme', [
            'baremes' => $baremes,
        ]);
    }

    public function nouveau()
    {
        return view('admin/configForm', [
            'bareme' => null,
        ]);
    }

    public function creer()
    {
        $donnees = $this->donneesFormulaire();

        $erreur = $this->valider($donnees);
        if ($erreur !== null) {
            return redirect()->back()->withInput()->with('error', $erreur);
        }

        (new ConfigModel())->insert($donnees);

        return redirect()->to('/admin/config')->with('success', 'Tranche créée.');
    }

    public function modifier(int $id)
    {
        $bareme = (new ConfigModel())->find($id);
        if (! $bareme) {
            return redirect()->to('/admin/config')->with('error', 'Tranche introuvable.');
        }

        return view('admin/configForm', [
            'bareme' => $bareme,
        ]);
    }

    public function mettreAJour(int $id)
    {
        $donnees = $this->donneesFormulaire();

        $erreur = $this->valider($donnees);
        if ($erreur !== null) {
            return redirect()->back()->withInput()->with('error', $erreur);
        }

        (new ConfigModel())->update($id, $donnees);

        return redirect()->to('/admin/config')->with('success', 'Tranche mise à jour.');
    }

    public function supprimer(int $id)
    {
        (new ConfigModel())->delete($id);
        return redirect()->to('/admin/config')->with('success', 'Tranche supprimée.');
    }

    private function donneesFormulaire(): array
    {
        return [
            'min'  => (float) $this->request->getPost('min'),
            'max'  => (float) $this->request->getPost('max'),
            'gain' => (float) $this->request->getPost('gain'),
        ];
    }

    private function valider(array $donnees): ?string
    {
        if ($donnees['min'] < 0 || $donnees['max'] <= 0) {
            return 'Les bornes min et max doivent être positives.';
        }
        if ($donnees['min'] >= $donnees['max']) {
            return 'Le min doit être inférieur au max.';
        }
        if ($donnees['gain'] < 0) {
            return 'Le gain ne peut pas être négatif.';
        }
        return null;
    }
}
