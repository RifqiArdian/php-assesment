<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TestController
{
    public function test(Request $request)
    {
        //validation input
        $validator = Validator::make($request->all(), [
            'text' => 'required|string',
            'index' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response()->json(["message"=>$validator->errors()->first()],400);
        }

        $result = null;
        $numberOfParenthesis = 0;
        $arrayText = str_split($request->text);

        //check the input index is an open parenthesis
        if($arrayText[$request->index]!='('){
            return response()->json(['message'=>'index input is not an open parenthesis'],400);
        }

        for ($i = 0; $i<count($arrayText);$i++){
            //check if there is open parenthesis after request index
            if($arrayText[$i] == '(' && $i > $request->index){
                $numberOfParenthesis++;
            }
            if ($arrayText[$i] == ')' && $i > $request->index){
                $numberOfParenthesis--;
            }

            //check if index string is close parenthesis and there is no more open parenthesis
            if($numberOfParenthesis == -1 && $arrayText[$i] == ')'){
                $result = $i;
                break;
            }
        }

        return response()->json(["result" => $result]);
    }
}
