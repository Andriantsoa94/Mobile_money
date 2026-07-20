<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TypeOperationModel;

class TypeOperationController extends BaseController
{
    public function index()
    {
        $types = (new TypeOperationModel())->orderBy('nom', 'ASC')->findAll();

        return view('admin/typesOperation', [
            'types' => $types,
        ]);
    }

    public function nouveau()
    {
        return view('admin/typeOperationForm', [
            'type' => null,
        ]);
    }

    public function creer()
    {
        $nom = trim((string) $this->request->getPost('nom'));

        if ($nom === '') {
            return redirect()->back()->withInput()->with('error', 'Le nom est obligatoire.');
        }

        (new TypeOperationModel())->insert([
            'nom'     => $nom,
            'isGain'  => $this->request->getPost('isGain') ? 1 : 0,
            'isActif' => 1,
        ]);

        return redirect()->to('/admin/types')->with('success', 'Type d\'opération créé.');
    }

    public function modifier(int $id)
    {
        $type = (new TypeOperationModel())->find($id);
        if (! $type) {
            return redirect()->to('/admin/types')->with('error', 'Type introuvable.');
        }

        return view('admin/typeOperationForm', ['type' => $type]);
    }

    public function mettreAJour(int $id)
    {
        $nom = trim((string) $this->request->getPost('nom'));

        if ($nom === '') {
            return redirect()->back()->withInput()->with('error', 'Le nom est obligatoire.');
        }

        (new TypeOperationModel())->update($id, [
            'nom'    => $nom,
            'isGain' => $this->request->getPost('isGain') ? 1 : 0,
        ]);

        return redirect()->to('/admin/types')->with('success', 'Type mis à jour.');
    }

    public function basculerActif(int $id)
    {
        (new TypeOperationModel())->basculerActif($id);
        return redirect()->to('/admin/types')->with('success', 'Statut mis à jour.');
    }

    public function supprimer(int $id)
    {
        (new TypeOperationModel())->delete($id);
        return redirect()->to('/admin/types')->with('success', 'Type supprimé.');
    }
}
