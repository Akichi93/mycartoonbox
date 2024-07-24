<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use App\Models\Partenaire;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PartenaireController extends Controller
{
    public function partenaires()
    {
        $respo = Admin::select('role')->where('id', Auth::guard('admin')->user()->id)->value('role');
        $partenaires = Partenaire::all();
        return view('admin.partenaires.partenaires')->with(compact('partenaires', 'respo'));
    }

    public function addEditPartenaire(Request $request, $id = null)
    {
       
        if ($id == "") {
            $title = 'Ajouter partenaire';
            // ajout de fonctionnalités
            $partenaire = new Partenaire();
            $partenairedata = array();

            $message = "Le partenaire a ete ajouté avec succès !";
        } else {
            $title = "Modifier partenaire";
            $partenairedata = Partenaire::where('id', $id)->first();
            $partenairedata = json_decode(json_encode($partenairedata), true);

            $partenaire = Partenaire::find($id);
            $message = "Le partenaire à été Modifé avec succès !";
        }
     
        if ($request->isMethod('post')) {
            $data = $request->all();
            $rules = [
                'nom_partenaire' => 'required',
                'logo' => 'required',
            ];

            $customMessage = [
                'nom_partenaire.required' => 'Le nom de la categorie est requis',
                'logo.required' => 'Veuillez choisir un logo',
            ];
            $this->validate($request, $rules, $customMessage);

            // Création d'object
            $periode = $data['periode'];
            $tarif = $data['tarif'];
            $avantage = $data['avantage'];

            $array = [];
            for ($i = 0; $i < count($periode); $i++) {
                $object = [
                    "periode" => $periode[$i],
                    "tarif" => $tarif[$i],
                    "description" => $avantage[$i],
                ];

                array_push($array, $object);
            }

            if ($request->hasfile('logo')) {
                $file = $request->file('logo');
                $extenstion = $file->getClientOriginalExtension();
                $filename = time() . '.' . $extenstion;
                $file->move('image/partenaire_images/', $filename);

                $partenaire->logo = $filename;
            }

            $partenaire->nom_partenaire = $data['nom_partenaire'];
            $partenaire->credential = [
                'bundle' =>  $array
            ];
            $partenaire->save();
            $request->session()->flash('success_message', $message);
            return redirect('/partenaires');
        }
        return view('admin.partenaires.add_edit_partenaire')->with(compact('title', 'partenairedata'));
    }

    public function desactivatepartenaire(Request $request, $id)
    {

        $partenaires = Partenaire::find($id);
        $partenaires->status = 1;
        $partenaires->save();

        $request->session()->flash('success_message', "Partenaire désactivé.");

        return back();
    }

    public function activatepartenaire(Request $request, $id)
    {
        $partenaires = Partenaire::find($id);
        $partenaires->status = 0;
        $partenaires->save();

        $request->session()->flash('success_message', "Partenaire activé.");

        return back();
    }
}
