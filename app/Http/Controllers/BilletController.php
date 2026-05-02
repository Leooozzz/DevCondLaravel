<?php

namespace App\Http\Controllers;

use App\Models\Billet;
use App\Models\Unit;
use Illuminate\Http\Request;

class BilletController extends Controller
{
    public function getAll(Request $request)
    {

        $array = ['error' =>  ''];

        $property = $request->input('property');
        if ($property) {
            $user = auth()->user();
            $unit = Unit::query()->where('id', $property)->where('id_owner', $user['id'])->count();

            if ($unit > 0) {
                $billets = Billet::query()->where('id_unit', $property)->get();

                foreach ($billets as $billetKey => $billetsValue) {
                    $billets[$billetKey]['file_url'] = asset('storage/' . $billetsValue['file_url']);
                }
            } else {
                $array['error'] = "This unit is not yours";
            }
        } else {
            $array['error'] = "Property is required";
        }

        return $array;
    }
}
