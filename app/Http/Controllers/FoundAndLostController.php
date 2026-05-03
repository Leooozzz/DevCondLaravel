<?php

namespace App\Http\Controllers;

use App\Models\FoundAndLost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FoundAndLostController extends Controller
{
    public function getAll()
    {
        $array = ['error' => ''];

        $lost = FoundAndLost::query()->where('status', 'LOST')->orderBy('date_created', 'DESC')->orderBy('id', 'DESC')->get();
        $recovered = FoundAndLost::query()->where('status', 'RECOVERED')->orderBy('date_created', 'DESC')->orderBy('id', 'DESC')->get();

        foreach ($lost as $lostKey => $lostValue) {
            $lost[$lostKey]['date_created'] = date('d/m/Y', strtotime($lostValue));
            $lost[$lostKey]['photo'] = asset('storage/' . $lostValue['photo']);
        }
        foreach ($recovered as $recoveredKey => $recoveredValue) {
            $recovered[$recoveredKey]['date_created'] = date('d/m/Y', strtotime($recoveredValue));
            $recovered[$recoveredKey]['photo'] = asset('storage/' . $recoveredValue['photo']);
        }
        $array['recovered'] = $recovered;
        $array['lost'] = $lost;

        return $array;
    }
    public function  insert(Request $request)
    {
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'description' => 'required',
            'where' => 'required',
            'photo' => 'required|file|mimes:jpg,png,jpeg'
        ]);
        if (!$validator->fails()) {
            $description = $request->input('description');
            $where = $request->input('where');
            $file = $request->file('photo')->store('public');

            $file = explode('public/', $file);
            $photo = $file[1];
            $newLost = new FoundAndLost();

            $newLost->status = 'LOST';
            $newLost->photo =  $photo;
            $newLost->where =  $where;
            $newLost->description =  $description;
            $newLost->date_created = date('Y-m-d');
            $newLost->save();
        } else {
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        return $array;
    }
    public function update($id, Request $request)
    {
        $array = ['error' => ''];

        $status = $request->input('status');
        if ($status && in_array($status, ['LOST', 'RECOVERED'])) {
            $item = FoundAndLost::find('id', $id);
            if (!$item) {
                $array['error'] = "Not exists";
            }
            $item->status = $status;
            $item->save();
        } else {
            $array['error'] = "Status not exists";
            return $array;
        }

        return $array;
    }
}
