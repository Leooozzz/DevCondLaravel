<?php

namespace App\Http\Controllers;

use App\Models\FoundAndLost;
use Illuminate\Http\Request;

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
    public function insert()
    {
        $array = ['error' => ''];



        return $array;
    }
    public function update()
    {
        $array = ['error' => ''];



        return $array;
    }
}
