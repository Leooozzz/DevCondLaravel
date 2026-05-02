<?php

namespace App\Http\Controllers;

use App\Models\Wall;
use App\Models\WallLike;
use Illuminate\Http\Request;

class WallController extends Controller
{
    public function getAll()
    {
        $array = ['error' => '', 'list' => []];

        $user = auth()->user();

        $walls = Wall::all();

        foreach ($walls as $wallkey => $wallValue) {
            $walls[$wallkey]['like'] = 0;
            $walls[$wallkey]['liked'] = false;

            $meLikes = WallLike::query()
                ->where('id_wall', $wallValue['id'])
                ->where('id_user', $user['id'])
                ->count();
            if ($meLikes > 0) {
                $walls[$wallkey]['liked'] = true;
            }
            $likes = WallLike::query()->where('id_wall', $wallValue['id'])->count();
            $walls[$wallkey]['like'] = $likes;
        }
        $array['list'] = $walls;

        return $array;
    }
    public function like(Request $request, $id) {}
}
