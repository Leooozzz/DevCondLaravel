<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\AreaDisabledDay;
use App\Models\Reservation;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReservationController extends Controller
{
    public function getReservation()
    {
        $array = ['error' => '', 'list' => []];
        $daysHelper = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];

        $areas = Area::query()->where('allowed', 1)->get();


        foreach ($areas as $area) {
            $dayList = explode(',', $area['days']);

            $dayGroups = [];

            $lastDay = intval(current($dayList));
            $dayGroups[] = $daysHelper[$lastDay];
            array_shift($dayList);

            foreach ($dayList as $day) {
                if (intval($day) != $lastDay + 1) {
                    $dayGroups[] = $daysHelper[$lastDay];
                    $dayGroups[] = $daysHelper[$day];
                }
                $lastDay = intval($day);
            }

            $dayGroups[] = $daysHelper[end($dayList)];

            $dates = '';
            $close = 0;
            foreach ($dayGroups as $group) {
                if ($close === 0) {
                    $dates .= $group;
                } else {
                    $dates .= '-' . $group . ',';
                }
                $close = 1 - $close;
            }
            $dates = explode(',', $dates);
            array_pop($dates);

            $start = date('H:i', strtotime($area['start_time']));
            $end = date('H:i', strtotime($area['end_time']));

            foreach ($dates as $dKey => $dValue) {
                $dates[$dKey] .= ' ' . $start . 'às' . $end;
            }
            $array['list'][] = [
                'id' => $area['id'],
                'cover' => asset('storage/' . $area['cover']),
                'title' => $area['title'],
                'dates' => $dates
            ];

            echo "AREA: " . $area['title'] . "\n";
            print_r($dayGroups);
            echo "\n-------";
        }


        return $array;
    }
    public function setReservation(Request $request, $id)
    {
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'date' => 'required|date_format:Y-m-d',
            'time' => 'required|date_format:H:i:s',
            'property' => 'required'
        ]);
        if (!$validator->fails()) {
            $date = $request->input('date');
            $time = $request->input('time');
            $property = $request->input('property');


            $unit = Unit::query()->find($property);
            $area = Area::query()->find($id);

            if ($unit && $area) {
                $can = true;
                $weekday = date('w', strtotime($date));

                $allowedDays = explode(',', $area['days']);
                if (!in_array($weekday, $allowedDays)) {
                    $can = false;
                } else {
                    $start = strtotime($area['start_time']);
                    $end = strtotime('-1 hour', strtotime($area['end_time']));
                    $revtime = strtotime($time);
                    if ($revtime < $time || $revtime > $end) {
                        $can = false;
                    }
                }
                $existingDisabledDay = AreaDisabledDay::query()->where('id_area', $id)->where('day', $date)->count();

                if ($existingDisabledDay > 0) {
                    $can = false;
                }
                $existingReservations = Reservation::query()->where('id_area', $id)->where('reservation_date', $date . ' ' . $time)->count();
                if ($existingReservations > 0) {
                    $can = false;
                }
                if ($can) {
                    $newReservation = new Reservation();
                    $newReservation->id_unit = $property;
                    $newReservation->id_area = $id;
                    $newReservation->reservation_date = $date . ' ' . $time;
                    $newReservation->save();
                } else {
                    $array['error'] = 'Cannot reservate';
                    return $array;
                }
            } else {
                $array['error'] = 'Invalid data';
                return $array;
            }
        } else {
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        return $array;
    }
    public function getDisableDates($id)
    {
        $array = ['error' =>  ''];
        $area = Area::query()->find($id);
        if ($area) {
            $disabledDays = AreaDisabledDay::query()->where('id_area', $id)->get();
            foreach ($disabledDays as $disabledDay) {
                $array['list'][] = $disabledDay['day'];
            }
            $allowedDays = explode(',', $area['days']);
            $offDays = [];
            for ($q = 0; $q < 7; $q++) {
                if (!in_array($q, $allowedDays)) {
                    $offDays[] = $q;
                }
            }
            $start = time();
            $end = strtotime('+3 months');
            $currentDay = $start;
            $keep = true;
            while ($keep) {
                if ($currentDay < $end) {
                    $wd = date('w', $currentDay);
                    if (in_array($wd, $offDays)) {
                        $array['list'][] = date('Y-m-d', $currentDay);
                    }
                    $currentDay = strtotime('+1  day', $currentDay);
                } else {
                    $keep = false;
                }
            }
        } else {
            $array['error'] = "Area not found ";
            return $array;
        }
        return $array;
    }
    public function  getTimes($id, Request $request)
    {
        $array = ['error' => '', 'list' => ''];

        $validator = Validator::make($request->all(), [
            'date' => 'required|date_format:Y-m-d',
        ]);
        if (!$validator->fails()) {
            $date = $request->input('date');
            $area = Area::query()->find($id);

            if ($area) {
                $can = true;

                $existingDisabledDay = AreaDisabledDay::query()->where('id_area', $id)->where('day', $date)->count();

                if ($existingDisabledDay > 0) {
                    $can = false;
                }

                $allowedDays = explode(',', $area['days']);
                $weekday = date('w', strtotime($date));
                if (!in_array($weekday, $allowedDays)) {
                    $can = false;
                }
                if ($can) {
                    $start = strtotime($area['start_time']);
                    $end = strtotime($area['end_time']);
                    $times = [];
                    for ($lastTime = $start; $lastTime < $end; $lastTime = strtotime('+1 hour', $lastTime)) {
                        $times[] = $lastTime;
                    }
                    $timeList = [];
                    foreach ($times as $time) {
                        $timeList[] = [
                            'id' => date('H:i:s', $time),
                            'title' => date('H:i', $time) . '-' . date('H:i', strtotime('+1 hour', $time)),

                        ];
                    }
                    $reservations = Reservation::query()->where('id_area', $id)->whereBetween('reservation_date', [
                        $date . '00:00:00',
                        $date . '23:59:59'
                    ])->get();

                    $toRemove = [];
                    foreach ($reservations as $reservation) {
                        $time = date('H:i:s', strtotime($reservation['reservation_date']));
                        $toRemove[] = $time;
                    }
                    foreach ($timeList as $timeItem) {
                        if (!in_array($timeItem['id'], $toRemove)) {
                            $array['list'] = $timeItem;
                        }
                    }

                    $array['list'] = $timeList;
                }
            } else {
                $array['error'] = 'non-existent area';
                return $array;
            }
        } else {
            $array['error'] = $validator->errors()->first();
            return $array;
        }


        return $array;
    }
    public function getMyReservation(){
        $array = ['error' => ''];


        

        return $array;

    }
}
