<?php

namespace App\Http\Controllers;

use App\Models\Doc;
use Illuminate\Http\Request;

class DocController extends Controller
{
    public function getAll()
    {
        $array = ['error' => '', 'list' => []];

        $user = auth()->user();

        $docs = Doc::all();

        foreach ($docs as $docsKey => $docValue) {
            $docs[$docsKey]['file_url'] = asset('storage/' . $docValue['file_url']);
        }
        $array['list'] = $docs;

        return $array;
    }
}
