<?php

namespace App\Http\Controllers;

use App\Bid;
use App\Call;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use App\Exceptions\CoreErrors;
use Illuminate\Support\Facades\DB;
// use App\OXOResponse;
use Oxoresponse\OXOResponse;

class BidController extends BaseController{

    // public function index(){

    //     $area = Bid::all();
       
    //     $OXOResponse = new \Oxoresponse\OXOResponse("Operation successful");
    //     $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
    //     $OXOResponse->setObject($area);

    //     return $OXOResponse->jsonSerialize();
    // }

    public function create(Request $request){

        try{

            $this->validate(
                $request, [
                    'user_id' => 'required',
                    'call_id' => 'required',
          
                ]
            );

            $bid = Bid::where(["user_id"=>$request->user_id,"call_id"=>$request->call_id])->first();

            if($bid === null){

                $bid = new Bid();

                $bid->user_id = $request->user_id;
                $bid->call_id = $request->call_id;
    
                if($bid->save()){
    
                    $call = Call::where('id',$request->call_id)->first();
                    $call->bids_count +=1;
                    $call->save();
    
                    $OXOResponse = new \Oxoresponse\OXOResponse("The application has been sent successfully");
                    $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
                    $OXOResponse->setObject($bid);
                    return $OXOResponse->jsonSerialize();
    
                } else {
    
                    $OXOResponse = new \Oxoresponse\OXOResponse("Failed to create send application. Kindly try again later");
                    $OXOResponse->setErrorCode(CoreErrors::FAILED_TO_CREATE_RECORD);
                    $OXOResponse->setObject($bid);
                    return $OXOResponse->jsonSerialize();
                }
            }
            else {

                $OXOResponse = new \Oxoresponse\OXOResponse("The application has been sent successfully");
                $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
                $OXOResponse->setObject($bid);
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

    public function checkUserExistance(Request $request){

        $bid = Bid::where(['user_id'=>$request->user_id,'call_id'=>$request->call_id])->firstOr(function()
        {

            $OXOResponse = new OXOResponse("Record not found");
            $OXOResponse->addErrorToList("make sure you have passed correct ID");
            $OXOResponse->setErrorCode(CoreErrors::RECORD_NOT_FOUND);

            return $OXOResponse;
         });

         if($bid instanceof OXOResponse){
    
             return $bid->jsonSerialize();
        } else {

            $OXOResponse = new OXOResponse("Record found");
            $OXOResponse->addErrorToList("make sure you have passed correct ID");
            $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);

            return $OXOResponse;
        }

    }

    public function fetchBids($id){

        $call = Call::where(['id'=> $id])->firstOr(function()
        {

            $OXOResponse = new OXOResponse("Record not found");
            $OXOResponse->addErrorToList("make sure you have passed correct ID");
            $OXOResponse->setErrorCode(CoreErrors::RECORD_NOT_FOUND);

            return $OXOResponse;
         });

        if($call instanceof OXOResponse){
    
            return $call->jsonSerialize();

        } else {

            // $bids = Bid::where('call_id',$id)->get();

            $bids = DB::table('bids')
                ->join('users','bids.user_id','=','users.id')
                ->where('bids.call_id',$id)
                ->get();

            if($bids !== null){

                $OXOResponse = new \Oxoresponse\OXOResponse("The call bids returned successfully");
                $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
                $OXOResponse->setObject($bids);
                return $OXOResponse->jsonSerialize();
            }
            else {

                $OXOResponse = new OXOResponse("Could not find the requested funder");
                $OXOResponse->addErrorToList("make sure you have passed correct id");
                $OXOResponse->setErrorCode(CoreErrors::RECORD_NOT_FOUND);
    
                return $OXOResponse;
            }
           
        }

    }

    public function awardBid(Request $request){

        $call= Call::where(['id'=>$request->call_id])->firstOr(function()
        {

            $OXOResponse = new OXOResponse("Record not found");
            $OXOResponse->addErrorToList("make sure you have passed correct ID");
            $OXOResponse->setErrorCode(CoreErrors::RECORD_NOT_FOUND);

            return $OXOResponse;
         });

         if($call instanceof OXOResponse){
    
            return $call->jsonSerialize();

        } else 
        {
            $call->user_id = $request->email;

            $call->status = 'awarded';

            if($call->save()){

                $OXOResponse = new \Oxoresponse\OXOResponse("The bid awarded successfully");
                $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
                $OXOResponse->setObject($call);
                return $OXOResponse->jsonSerialize();

            }else {

                $OXOResponse = new \Oxoresponse\OXOResponse("The process of awarding has failed");
                $OXOResponse->setErrorCode(CoreErrors::UPDATE_OPERATION_FAILED);
                $OXOResponse->setObject($call);
                return $OXOResponse->jsonSerialize();
            }
        }
    }

    public function cancelAward(Request $request){

        $call= Call::where(['id'=>$request->call_id])->firstOr(function()
        {

            $OXOResponse = new OXOResponse("Record not found");
            $OXOResponse->addErrorToList("make sure you have passed correct ID");
            $OXOResponse->setErrorCode(CoreErrors::RECORD_NOT_FOUND);

            return $OXOResponse;
         });

         if($call instanceof OXOResponse){
    
            return $call->jsonSerialize();

        } else 
        {
            $call->user_id = null;

            $call->status = 'open';

            if($call->save()){

                $OXOResponse = new \Oxoresponse\OXOResponse("The award cancelled successfully");
                $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
                $OXOResponse->setObject($call);
                return $OXOResponse->jsonSerialize();

            }else {

                $OXOResponse = new \Oxoresponse\OXOResponse("The process of cancelling award has failed");
                $OXOResponse->setErrorCode(CoreErrors::UPDATE_OPERATION_FAILED);
                $OXOResponse->setObject($call);
                return $OXOResponse->jsonSerialize();
            }
        }
    }
    // public function update(Request $request){

    //     try{

    //         $this->validate($request, [
    //             'name' => 'required|string',
                
    //         ]);
            
    //         $area = Area::where('id',$request->id)->firstOr(function(){

    //             $OXOResponse = new OXOResponse("Could not find the requested funder");
    //             $OXOResponse->addErrorToList("make sure you have passed correct id");
    //             $OXOResponse->setErrorCode(CoreErrors::RECORD_NOT_FOUND);
    
    //             return $OXOResponse;
    //         });
    
    //         if($area instanceof OXOResponse){
    
    //             return $area->jsonSerialize();
    //         } else {
                
    //             $area->name = $request->name;

    //             if($request->has('description'))
    //                 $area->description = $request->description;

    //             if($area->save()){

    //                 $OXOResponse = new OXOResponse("Area updated successfully");
    //                 $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
    //                 $OXOResponse->setObject($area);
    //                 return $OXOResponse->jsonSerialize();

    //             } else {

    //                 $OXOResponse = new OXOResponse("Failed to update a area. Kindly try again later");
    //                 $OXOResponse->setErrorCode(CoreErrors::UPDATE_OPERATION_FAILED);
    //                 $OXOResponse->setObject($area);
    //                 return $OXOResponse->jsonSerialize();
    //             }
    //         }
    //     }catch(ValidationException $e){

    //         $OXOResponse = new OXOResponse("A validation exception has occured");
    //         $OXOResponse->addMessage("Make sure you have passed correct data with correct format");
    //         $OXOResponse->setErrorCode(CoreErrors::VALIDATION_ERROR);
    //         $arrayResponse =  $e->getResponse();
    //         $OXOResponse->setObject((array_column(array($arrayResponse), 'original')));
    //         return $OXOResponse->jsonSerialize();
    //     }  
    // }

    // public function delete(Request $request){

    //         $area = Area::where('id',$request->id)->firstOr(function(){

    //             $OXOResponse = new OXOResponse("Record not found");
    //             $OXOResponse->addErrorToList("make sure you have passed correct ID");
    //             $OXOResponse->setErrorCode(CoreErrors::RECORD_NOT_FOUND);

    //             return $OXOResponse;
    //         });

    //         if($area instanceof OXOResponse){

    //             return $area->jsonSerialize();
    //         }
    //         else {

    //             if($area->delete()){

    //                 $OXOResponse = new OXOResponse("Area deleted successful");
    //                 $OXOResponse->addErrorToList("AREA ID :: $request->id");
    //                 $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
    
    //                 $OXOResponse->setObject($area);
    
    //                 return $OXOResponse->jsonSerialize();

    //             } else {

    //                 $OXOResponse = new OXOResponse("Failed to delete a funder");
    //                 $OXOResponse->addErrorToList("AREA ID :: $request->id");
    //                 $OXOResponse->setErrorCode(CoreErrors::DELETE_OPERATION_FAILED);
    
    //                 $OXOResponse->setObject($area);
    
    //                 return $OXOResponse->jsonSerialize();
    //             }
    //         }

    // }

}