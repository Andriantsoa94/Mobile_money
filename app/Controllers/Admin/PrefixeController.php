<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OperateurModel;
use App\Models\PrefixeModel;

class PrefixeController extends BaseController
{
    public function index()
    {
        $prefixeModel = new PrefixeModel();

        $prefixes = $prefixeModel->findAllMe();

        return view('admin/prefixes', [
            'prefixes' => $prefixes,
        ]);
    }

    public function nouveau()
    {
        return view('admin/prefixeForm', [
            'prefixe'    => null,
            'operateurs' => (new OperateurModel())->orderBy('nom', 'ASC')->findAll(),
        ]);
    }

    public function creer()
    {
        $donnees = [
            'numero'      => trim((string) $this->request->getPost('numero')),
            'idoperateur' => (int) $this->request->getPost('idoperateur') ?: null,
        ];

        if (! preg_match('/^[0-9]{3}$/', $donnees['numero'])) {
            return redirect()->back()->withInput()->with('error', 'Le préfixe doit contenir 3 chiffres.');
        }

        (new PrefixeModel())->insert($donnees);

        return redirect()->to('/admin/prefixes')->with('success', 'Préfixe créé.');
    }

    public function modifier(int $id)
    {
        $prefixeModel = new PrefixeModel();
        $prefixe      = $prefixeModel->find($id);

        if (! $prefixe) {
            return redirect()->to('/admin/prefixes')->with('error', 'Préfixe introuvable.');
        }

        return view('admin/prefixeForm', [
            'prefixe'    => $prefixe,
            'operateurs' => (new OperateurModel())->orderBy('nom', 'ASC')->findAll(),
        ]);
    }

    public function mettreAJour(int $id)
    {
        $donnees = [
            'numero'      => trim((string) $this->request->getPost('numero')),
            'idoperateur' => (int) $this->request->getPost('idoperateur') ?: null,
        ];

        if (! preg_match('/^[0-9]{3}$/', $donnees['numero'])) {
            return redirect()->back()->withInput()->with('error', 'Le préfixe doit contenir 3 chiffres.');
        }

        (new PrefixeModel())->update($id, $donnees);

        return redirect()->to('/admin/prefixes')->with('success', 'Préfixe mis à jour.');
    }

    public function supprimer(int $id)
    {
        (new PrefixeModel())->delete($id);
        return redirect()->to('/admin/prefixes')->with('success', 'Préfixe supprimé.');
    }
}
