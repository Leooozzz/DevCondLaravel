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
    public function like($id)
    {
        $array = ['error' => ''];

        $user = auth()->user();

        $meLikes = WallLike::query()
            ->where('id_wall', $id)
            ->where('id_user', $user['id'])
            ->count();

        if ($meLikes > 0) {
            WallLike::query()->where('id', $id)->where('id_user', $user['id'])->delete();
            $array['liked'] = false;
        } else {
            $newLike = new WallLike();
            $newLike->id_wall = $id;
            $newLike->id_user = $user['id'];
            $newLike->save();
            $array['liked'] = true;
        }
        $array['likes'] = WallLike::query()->where('id_wall', $id)->count();

        return $array;
    }
}
