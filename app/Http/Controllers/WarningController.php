<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Warning;
use Illuminate\Http\Request;
use Storage;
use Validator;

class WarningController extends Controller
{
    public function getMyWarnings(Request $request)
    {
        $array = ['error' => ''];

        $property = $request->input('property');
        if (!$property) {
            $array = ['error' => 'property is required'];
        }
        $user = auth()->user();

        $unit = Unit::query()->where('id', $property)->where('id_owner', $user['id'])->count();

        if ($unit > 0) {
            $warning = Warning::query()->where('id_unit', $property)->orderBy('date_created', 'DESC')->orderBy('id', 'DESC')->get();

            foreach ($warning as $warnKey => $warnValues) {
                $warning[$warnKey]['date_created'] = date('d/m/Y', strtotime($warnValues['date_created']));
                $photos = [];
                $photos = explode(',', $warnValues['photos']);
                foreach ($photos as $photo) {
                    if (!empty($photo)) {
                        $photos[] = asset('storage/' . $photo);
                    }
                }
                $warning[$warnKey]['photos'] = $photos;
            }
            $array['list'] = $warning;
        } else {
            $array = ['error' => 'This property is not yours'];
        }
        return $array;
    }
    public function addWarningFile(Request $request)
    {
        $array = ['error' => ''];


        $validator = Validator::make($request->all, [
            'photo' => 'required|file|mimes:jpg,png,jpeg',
        ]);
        if (!$validator->fails()) {
            $file = $request->file('photo')->store('public');

            $array['photo'] = asset(Storage::url($file));
        } else {
            $array['error'] = $validator->errors()->first();
            return $array;
        }
        return $array;
    }
    public function setWarning(Request $request)
    {
        $array = ['error' => ''];

        $validator = Validator::make($request->all, [
            'title' => 'required',
            'property' => 'required'
        ]);

        if (!$validator->fails()) {
            $title = $request->input('title');
            $property = $request->input('property');
            $list = $request->input('list');

            $newWarning = new Warning();
            $newWarning->id_unit = $property;
            $newWarning->title = $title;
            $newWarning->status = 'IN_PREVIEW';
            $newWarning->date_created = date('Y-m-d');

            if ($list && is_array($list)) {
                $photos = [];
                foreach ($list as $listItem) {
                    $url = explode('/', $listItem);
                    $photos[] = end($url);
                }
                $newWarning->photos = implode(',', $photos);
            } else {
                $newWarning->photos = '';
            }
            $newWarning->save();
        } else {
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        return $array;
    }
}
