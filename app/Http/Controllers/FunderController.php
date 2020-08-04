<?php

namespace App\Http\Controllers;

use App\Funder;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use App\Exceptions\CoreErrors;
use Illuminate\Support\Facades\DB;
// use App\OXOResponse;
use Oxoresponse\OXOResponse;

class FunderController extends BaseController{

    public function index(){

        $funder = Funder::all();
       
        $OXOResponse = new \Oxoresponse\OXOResponse("Operation successful");
        $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
        $OXOResponse->setObject($funder);

        return $OXOResponse->jsonSerialize();
    }

    public function getFunder($id){

        $funder = Funder::where('id',$id)->firstOr(function(){

            $OXOResponse = new OXOResponse("Could not find the requested funder");
            $OXOResponse->addErrorToList("make sure you have passed correct id");
            $OXOResponse->setErrorCode(CoreErrors::RECORD_NOT_FOUND);

            return $OXOResponse;
        });

        if($funder instanceof OXOResponse){
    
            return $funder->jsonSerialize();
        } else
        {
            

            $OXOResponse = new \Oxoresponse\OXOResponse("Operation successful");
            $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
            $OXOResponse->setObject($funder);

            return $OXOResponse->jsonSerialize();
        }

    }

    public function getNames(){

        $names = DB::table('funders')->select('name','id')->get();

        return response()->json($names);
    }

    public function addFunder(Request $request){

        try{

            $this->validate(
                $request, [
                    'name' => 'required|string',
                    'address' => 'required|string'
                ]
            );

            $funder = new Funder();
            $funder->name = $request->name;
            $funder->location = $request->address;

            if($funder->save()){

                $OXOResponse = new \Oxoresponse\OXOResponse("Funder created successfully");
                $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
                $OXOResponse->setObject($funder);
                return $OXOResponse->jsonSerialize();

            } else {

                $OXOResponse = new \Oxoresponse\OXOResponse("Failed to create funder. Kindly try again later");
                $OXOResponse->setErrorCode(CoreErrors::FAILED_TO_CREATE_RECORD);
                $OXOResponse->setObject($funder);
                return $OXOResponse->jsonSerialize();
            }
            
        }catch (ValidationException $e) {
            $OXOResponse = new OXOResponse("A validation exception has occured");
            $OXOResponse->addMessage("Make sure you have passed correct data with correct format");
            $OXOResponse->setErrorCode(CoreErrors::VALIDATION_ERROR);
            $arrayResponse =  $e->getResponse();
            $OXOResponse->setObject((array_column(array($arrayResponse), 'original')));
            return $OXOResponse->jsonSerialize();
        }

    }

    public function update(Request $request){

        try{

            $this->validate($request, [
                'name' => 'required|string',
                'address' => 'required|string'
            ]);

            $funder = Funder::where('id',$request->id)->firstOr(function(){

                $OXOResponse = new OXOResponse("Could not find the requested funder");
                $OXOResponse->addErrorToList("make sure you have passed correct id");
                $OXOResponse->setErrorCode(CoreErrors::RECORD_NOT_FOUND);
    
                return $OXOResponse;
            });
    
            if($funder instanceof OXOResponse){
    
                return $funder->jsonSerialize();
            } else {
                
                $funder->name = $request->name;
                $funder->location = $request->address;

                if($funder->save()){

                    $OXOResponse = new OXOResponse("Funder updated successfully");
                    $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
                    $OXOResponse->setObject($funder);
                    return $OXOResponse->jsonSerialize();

                } else {

                    $OXOResponse = new OXOResponse("Failed to update a funder. Kindly try again later");
                    $OXOResponse->setErrorCode(CoreErrors::UPDATE_OPERATION_FAILED);
                    $OXOResponse->setObject($funder);
                    return $OXOResponse->jsonSerialize();
                }
            }
        }catch(ValidationException $e){

            $OXOResponse = new OXOResponse("A validation exception has occured");
            $OXOResponse->addMessage("Make sure you have passed correct data with correct format");
            $OXOResponse->setErrorCode(CoreErrors::VALIDATION_ERROR);
            $arrayResponse =  $e->getResponse();
            $OXOResponse->setObject((array_column(array($arrayResponse), 'original')));
            return $OXOResponse->jsonSerialize();
        }  
    }

    public function delete(Request $request){

            $funder = Funder::where('id',$request->id)->firstOr(function(){

                $OXOResponse = new OXOResponse("Record not found");
                $OXOResponse->addErrorToList("make sure you have passed correct ID");
                $OXOResponse->setErrorCode(CoreErrors::RECORD_NOT_FOUND);

                return $OXOResponse;
            });

            if($funder instanceof OXOResponse){

                return $funder->jsonSerialize();
            }
            else {

                if($funder->delete()){

                    $OXOResponse = new OXOResponse("Funder deleted successful");
                    $OXOResponse->addErrorToList("FUNDER ID :: $request->id");
                    $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
    
                    $OXOResponse->setObject($funder);
    
                    return $OXOResponse->jsonSerialize();

                } else {

                    $OXOResponse = new OXOResponse("Failed to delete a funder");
                    $OXOResponse->addErrorToList("FUNDER ID :: $request->id");
                    $OXOResponse->setErrorCode(CoreErrors::DELETE_OPERATION_FAILED);
    
                    $OXOResponse->setObject($funder);
    
                    return $OXOResponse->jsonSerialize();
                }
            }

    }

}