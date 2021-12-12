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

        //parse excel file to array
        $data = Excel::toArray(new TestImport,$request->file);
        $data = $data[0];
        $result = [];

        if($this->checkAllValueInArrayIsNull(end($data))){
            unset($data[array_key_last($data)]);
        }

        for ($i = 1; $i < count($data); $i++){
            $messages = null;

            for ($j = 0; $j < count($data[0]); $j++){
                //validate column value by column header name
                if(strpos($data[0][$j],'*') !== false){
                    if($data[$i][$j] == null){
                        $messages = $messages.' missing value in '.$data[0][$j].', ';
                    }
                }elseif (strpos($data[0][$j],'#') !== false){
                    if(strpos($data[$i][$j]," ") !== false){
                        $messages = $messages.$data[0][$j].' should not contain any space, ';
                    }
                }

            }

            //if there is error in validation row push message to array
            if($messages) {
                $error = [
                    'row' => $i+1,
                    'error' => $messages,
                ];
                array_push($result, $error);
            }

        }

        return response()->json($result);
    }

    public function checkAllValueInArrayIsNull($data){
        for ($i = 0; $i<count($data); $i++){
            if($data[$i]){
                return false;
            }
        }
        return true;
    }
}
