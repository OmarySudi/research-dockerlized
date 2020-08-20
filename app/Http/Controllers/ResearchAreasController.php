<?php

namespace App\Http\Controllers;

use App\Area;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use App\Exceptions\CoreErrors;
use App\OXOResponse;

class ResearchAreasController extends BaseController{

    public function index(){

        $areas = Area::all();
       
        $OXOResponse = new \Oxoresponse\OXOResponse("Operation successful");
        $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
        $OXOResponse->setObject($areas);

        return $OXOResponse->jsonSerialize();
    }

}