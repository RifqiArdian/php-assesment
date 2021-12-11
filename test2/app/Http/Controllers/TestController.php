<?php

namespace App\Http\Controllers;

use App\Imports\TestImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class TestController extends Controller
{
    public function test(Request $request){
        //validation input
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xls,xlsx',
        ]);
        if ($validator->fails()) {
            return response()->json(["message"=>$validator->errors()->first()],400);
        }

        $data = Excel::toArray(new TestImport,$request->file);
        $data = $data[0];
        $result = [];
        $rowError = null;

        for ($i = 1; $i < count($data); $i++){
            $messages = null;

            for ($j = 0; $j < count($data[0]); $j++){

                if(strpos($data[0][$j],'*') !== false){
                    if($data[$i][$j] == null){
                        $rowError = $j;
                        $messages = $messages.' missing value in '.$data[0][$j].', ';
                    }
                }elseif (strpos($data[0][$j],'#') !== false){
                    if(strpos($data[$i][$j]," ") !== false){
                        $rowError = $j;
                        $messages = $messages.$data[0][$j].' should not contain any space, ';
                    }
                }

            }

            if($messages) {
                $error = [
                    'row' => $rowError,
                    'error' => $messages,
                ];
                array_push($result, $error);
            }

        }

        return response()->json($result);
    }
}
