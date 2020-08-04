<?php

namespace App\Http\Controllers;

use App\Call;
use App\Funder;
use App\AreaCall;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use App\Exceptions\CoreErrors;
use Illuminate\Support\Facades\DB;
//use App\OXOResponse;
use Oxoresponse\OXOResponse;

class CallController extends BaseController{

    public function create(Request $request){

        try{

            $this->validate(
                $request, [
                    'funder' => 'required|string',
                    'budget' => 'required|string',
                    'deadline' => 'required|string',
                    'description' => 'required|string',
                    'status' => 'required|string',
                    'areas_of_research' => 'required',
                    'areas_of_research_names' => 'required'
                ]
            );

            $funder_id = Funder::where('name',$request->funder)->value('id');

            $call = new Call();
      
            $call->funder_id = $funder_id;
            $call->budget = $request->get('budget');
            $call->deadline = $request->get('deadline');
            $call->description = $request->get('description');
            $call->status = $request->get('status');
            $call->areas_of_research_names = $request->areas_of_research_names;

            $areas = $request->areas_of_research_names;

            if($request->has('area_1'))
            {
                $call->area_1 = $request->area_1;
                array_push($areas,$request->area_1);

            }
                
            if($request->has('area_2'))
            {
                $call->area_2 = $request->area_2;
                array_push($areas,$request->area_2);

            }

            if($request->has('area_3'))
            {
                $call->area_3 = $request->area_3;
                array_push($areas,$request->area_3);

            }

            $call->areas_of_research = implode(", ",$areas);
                
            if($call->save()):

                $OXOResponse = new \Oxoresponse\OXOResponse("Call created successfully");
                $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
                $OXOResponse->setObject($call);

                foreach($request->areas_of_research as $area){

                    $area_call = new AreaCall();
        
                    $area_call->area_id = intval($area);
        
                    $area_call->call_id = $call->id;
        
                    $area_call->save();
                }
                
                return $OXOResponse->jsonSerialize();
            else:
                $OXOResponse = new \Oxoresponse\OXOResponse("Failed to create user. Kindly try again later");
                $OXOResponse->setErrorCode(CoreErrors::FAILED_TO_CREATE_RECORD);
                $OXOResponse->setObject($call);
                return $OXOResponse->jsonSerialize();
            endif;

        }catch(ValidationException $e){

            $OXOResponse = new OXOResponse("A validation exception has occured");
            $OXOResponse->addMessage("Make sure you have passed correct data with correct format");
            $OXOResponse->setErrorCode(CoreErrors::VALIDATION_ERROR);
            $arrayResponse =  $e->getResponse();
            $OXOResponse->setObject((array_column(array($arrayResponse), 'original')));
            return $OXOResponse->jsonSerialize();
        }
    }

    public function index(){

        $calls = DB::table('calls')->join('funders','calls.funder_id','=','funders.id')
                            ->select('calls.*','funders.name')->get();

        $OXOResponse = new \Oxoresponse\OXOResponse("Operation successful");
        $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
        $OXOResponse->setObject($calls);

        return $OXOResponse->jsonSerialize();

    }
    
    public function getCall($id){

    
        $call = DB::table('calls')
                    ->join('funders', function ($join) use ($id){
                        $join->on('calls.funder_id', '=', 'funders.id')
                            ->where('calls.id', '=', $id);
                    })
                    ->get();

        if($call != null){

            $OXOResponse = new \Oxoresponse\OXOResponse("Operation successful");
            $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
            $OXOResponse->setObject($call);

            return $OXOResponse->jsonSerialize();
        }
        else {

            $OXOResponse = new OXOResponse("Could not find the requested funder");
            $OXOResponse->addErrorToList("make sure you have passed correct id");
            $OXOResponse->setErrorCode(CoreErrors::RECORD_NOT_FOUND);

            return $OXOResponse;

        }

       
        

        // if($call instanceof OXOResponse){
    
        //     return $call->jsonSerialize();
        // } else
        // {
            

        //     $OXOResponse = new \Oxoresponse\OXOResponse("Operation successful");
        //     $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
        //     $OXOResponse->setObject($call);

        //     return $OXOResponse->jsonSerialize();
        // }

    }

    public function getCallInfo($id){

    
        $call = Call::where('id',$id)->firstOr(function(){

            $OXOResponse = new OXOResponse("Could not find the requested call");
            $OXOResponse->addErrorToList("make sure you have passed correct id");
            $OXOResponse->setErrorCode(CoreErrors::RECORD_NOT_FOUND);

            return $OXOResponse;
        });

        if($call instanceof OXOResponse){
    
            return $call->jsonSerialize();

        } else {

            $OXOResponse = new \Oxoresponse\OXOResponse("Operation successful");
            $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
            $OXOResponse->setObject($call);

            return $OXOResponse->jsonSerialize();

        }
    }

    public function update($id,Request $request){

        $funder_id = Funder::where('name',$request->funder)->value('id');

        $call = Call::where('id',$id)->firstOr(function(){

            $OXOResponse = new OXOResponse("Could not find the requested call");
            $OXOResponse->addErrorToList("make sure you have passed correct id");
            $OXOResponse->setErrorCode(CoreErrors::RECORD_NOT_FOUND);

            return $OXOResponse;
        });

        if($call instanceof OXOResponse){
    
            return $call->jsonSerialize();

        } else
        {
            $call->funder_id = $funder_id;
            $call->budget = $request->get('budget');
            $call->deadline = $request->get('deadline');
            $call->description = $request->get('description');
            $call->status = $request->get('status');
            $call->areas_of_research_names = $request->areas_of_research_names;

            $areas = $request->areas_of_research_names;

            if($request->has('area_1'))
            {
                $call->area_1 = $request->area_1;
                array_push($areas,$request->area_1);

            }
                
            if($request->has('area_2'))
            {
                $call->area_2 = $request->area_2;
                array_push($areas,$request->area_2);

            }

            if($request->has('area_3'))
            {
                $call->area_3 = $request->area_3;
                array_push($areas,$request->area_3);

            }

            $call->areas_of_research = implode(", ",$areas);
                
            if($call->save()):

                $OXOResponse = new \Oxoresponse\OXOResponse("Call updated successfully");
                $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
                $OXOResponse->setObject($call);


                AreaCall::where('call_id',$id)->delete();

                foreach($request->areas_of_research as $area){

                    $area_call = new AreaCall();
        
                    $area_call->area_id = intval($area);
        
                    $area_call->call_id = $call->id;
        
                    $area_call->save();
                }
                
                return $OXOResponse->jsonSerialize();
            else:
                $OXOResponse = new \Oxoresponse\OXOResponse("Failed to create user. Kindly try again later");
                $OXOResponse->setErrorCode(CoreErrors::UPDATE_OPERATION_FAILED);
                $OXOResponse->setObject($call);
                return $OXOResponse->jsonSerialize();
            endif;
        }


    }
    public function delete($id){

            $call = Call::where('id',$id)->firstOr(function(){

                $OXOResponse = new OXOResponse("Record not found");
                $OXOResponse->addErrorToList("make sure you have passed correct ID");
                $OXOResponse->setErrorCode(CoreErrors::RECORD_NOT_FOUND);

                return $OXOResponse;
            });

            if($call instanceof OXOResponse){

                return $call->jsonSerialize();
            }
            else {

                if($call->delete()){

                    $OXOResponse = new OXOResponse("Call deleted successful");
                    $OXOResponse->addErrorToList("CALL ID :: $id");
                    $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
    
                    $OXOResponse->setObject($call);
    
                    return $OXOResponse->jsonSerialize();

                } else {

                    $OXOResponse = new OXOResponse("Failed to delete a Call");
                    $OXOResponse->addErrorToList("CALL ID :: $id");
                    $OXOResponse->setErrorCode(CoreErrors::DELETE_OPERATION_FAILED);
    
                    $OXOResponse->setObject($call);
    
                    return $OXOResponse->jsonSerialize();
                }
            }

    }

}