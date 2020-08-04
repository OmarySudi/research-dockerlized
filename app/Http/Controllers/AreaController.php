<?php

namespace App\Http\Controllers;

use App\Area;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use App\Exceptions\CoreErrors;
use Illuminate\Support\Facades\DB;
// use App\OXOResponse;
use Oxoresponse\OXOResponse;

class AreaController extends BaseController{

    public function index(){

        $area = Area::all();
       
        $OXOResponse = new \Oxoresponse\OXOResponse("Operation successful");
        $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
        $OXOResponse->setObject($area);

        return $OXOResponse->jsonSerialize();
    }

    public function create(Request $request){

        try{

            $this->validate(
                $request, [
                    'name' => 'required|string',
          
                ]
            );

            $area = new Area();
            $area->name = $request->name;

            if($request->has('description'))
                $area->description = $request->description;

            if($area->save()){

                $OXOResponse = new \Oxoresponse\OXOResponse("Area created successfully");
                $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
                $OXOResponse->setObject($area);
                return $OXOResponse->jsonSerialize();

            } else {

                $OXOResponse = new \Oxoresponse\OXOResponse("Failed to create area. Kindly try again later");
                $OXOResponse->setErrorCode(CoreErrors::FAILED_TO_CREATE_RECORD);
                $OXOResponse->setObject($area);
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
                
            ]);
            
            $area = Area::where('id',$request->id)->firstOr(function(){

                $OXOResponse = new OXOResponse("Could not find the requested funder");
                $OXOResponse->addErrorToList("make sure you have passed correct id");
                $OXOResponse->setErrorCode(CoreErrors::RECORD_NOT_FOUND);
    
                return $OXOResponse;
            });
    
            if($area instanceof OXOResponse){
    
                return $area->jsonSerialize();
            } else {
                
                $area->name = $request->name;

                if($request->has('description'))
                    $area->description = $request->description;

                if($area->save()){

                    $OXOResponse = new OXOResponse("Area updated successfully");
                    $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
                    $OXOResponse->setObject($area);
                    return $OXOResponse->jsonSerialize();

                } else {

                    $OXOResponse = new OXOResponse("Failed to update a area. Kindly try again later");
                    $OXOResponse->setErrorCode(CoreErrors::UPDATE_OPERATION_FAILED);
                    $OXOResponse->setObject($area);
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

            $area = Area::where('id',$request->id)->firstOr(function(){

                $OXOResponse = new OXOResponse("Record not found");
                $OXOResponse->addErrorToList("make sure you have passed correct ID");
                $OXOResponse->setErrorCode(CoreErrors::RECORD_NOT_FOUND);

                return $OXOResponse;
            });

            if($area instanceof OXOResponse){

                return $area->jsonSerialize();
            }
            else {

                if($area->delete()){

                    $OXOResponse = new OXOResponse("Area deleted successful");
                    $OXOResponse->addErrorToList("AREA ID :: $request->id");
                    $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
    
                    $OXOResponse->setObject($area);
    
                    return $OXOResponse->jsonSerialize();

                } else {

                    $OXOResponse = new OXOResponse("Failed to delete a funder");
                    $OXOResponse->addErrorToList("AREA ID :: $request->id");
                    $OXOResponse->setErrorCode(CoreErrors::DELETE_OPERATION_FAILED);
    
                    $OXOResponse->setObject($area);
    
                    return $OXOResponse->jsonSerialize();
                }
            }

    }

}