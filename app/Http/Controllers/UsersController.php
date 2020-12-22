<?php

namespace App\Http\Controllers;

use App\User;
use App\AreaUser;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use App\Exceptions\CoreErrors;
use App\OXOResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
// use Aws\Exception\MultipartUploadException;
use Illuminate\Support\Facades\Hash;
// use Aws\S3\MultipartUploader;
// use Aws\S3\S3Client;

class UsersController extends BaseController{

   
    public function index(){

        $user = User::all();
       

        $OXOResponse = new \Oxoresponse\OXOResponse("Operation successful");
        $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
        $OXOResponse->setObject($user);

        return $OXOResponse->jsonSerialize();
    }


    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard()->factory()->getTTL() * 60
        ]);
    }


    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function guard()
    {
        return Auth::guard();
    }


    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken($this->guard()->refresh());
    }


    /**
     * Log the user out (Invalidate the token)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $this->guard()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }


    /**
     * Get the authenticated User
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        $response = response()->json($this->guard()->user());
        $OXOResponse = new \Oxoresponse\OXOResponse("User Found");
        $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
        $OXOResponse->setObject($response);
        return $OXOResponse->jsonSerialize();
    }

    public function login(Request $request) {
        
        $this->validate($request, [
            'email'     => 'required|email',
            'password'  => 'required'
        ]);

        // Find the user by email
        $profile = User::where('email', $request->input('email'))->first();
        if ($profile != null):
            if (Hash::check($request->input('password'), $profile->password)) {

                $credentials = $request->only('email', 'password');
               
                if ($token = Auth::attempt($credentials)) {
                    $object = $this->respondWithToken($token);
                    $OXOResponse = new \Oxoresponse\OXOResponse("Login Successfully");
                    $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
                    $OXOResponse->setObject($object);
                    return $OXOResponse->jsonSerialize();
                }
            } else {
                $OXOResponse = new \Oxoresponse\OXOResponse("Failed to login in.");
                $OXOResponse->setErrorCode(CoreErrors::USER_NOT_FOUND);
                $OXOResponse->addErrorToList("Kindly check your credentials and try again");
                return $OXOResponse;

            }
        else:
                $OXOResponse = new \Oxoresponse\OXOResponse("Kindly check your email. ");
                $OXOResponse->addErrorToList("Email does not exist");
                $OXOResponse->setErrorCode(CoreErrors::USER_NOT_FOUND);
                
                return $OXOResponse;
        endif;
        
    }

    public function changePassword(Request $request){

        $this->validate($request, [
            'email'     => 'required|email',
            'password'  => 'required'
        ]);

        $profile = User::where('email', $request->input('email'))->first();

        if ($profile != null):
          
            $profile->password = app('hash')->make($request->password);
            
            if($profile->save()){

                $OXOResponse = new \Oxoresponse\OXOResponse("You password has been updated successfully");
                $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
                $OXOResponse->setObject($profile);

                return $OXOResponse->jsonSerialize();

            }else {

                $OXOResponse = new \Oxoresponse\OXOResponse("There is error during changing of password,try again later");
                $OXOResponse->setErrorCode(CoreErrors::UPDATE_OPERATION_FAILED);
                $OXOResponse->addErrorToList("There is internal server error");
                return $OXOResponse;
            }

        else:
                $OXOResponse = new \Oxoresponse\OXOResponse("Kindly check your email. ");
                $OXOResponse->addErrorToList("Email does not exist");
                $OXOResponse->setErrorCode(CoreErrors::USER_NOT_FOUND);
                
                return $OXOResponse;
        endif;
    }

    public function getUserByEmail($email){

        $profile = User::where('email', $email)->first();

        if ($profile != null):
          
            $OXOResponse = new \Oxoresponse\OXOResponse("User exists");
            $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
            $OXOResponse->setObject($profile);

            return $OXOResponse->jsonSerialize();

        else:
                $OXOResponse = new \Oxoresponse\OXOResponse("Kindly check your email. Email does not exists");
                $OXOResponse->addErrorToList("Email does not exist");
                $OXOResponse->setErrorCode(CoreErrors::USER_NOT_FOUND);
                
                return $OXOResponse;
        endif;
    }
    
    public function create(Request $request){
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $this->validate(
            $request, [
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'email' => 'required|string',
                'password' => 'required|string',
                'password_confirm' => 'required|string',
                'faculty' => 'required|string',
                'department' => 'required|string',
                'mobile_number' => 'required|string',
                'research_system_admin_admin' => 'required|boolean',
                // 'areas_of_research' => 'required',
                // 'areas_of_research_names' => 'required'
            ]
        );

        $profile = User::where('email', $request->input('email'))->first();
        
        if($profile !== null){

            $OXOResponse = new \Oxoresponse\OXOResponse("The email already in use, please use another email");
            $OXOResponse->setErrorCode(CoreErrors::EMAIL_FOUND);
            $OXOResponse->setObject($profile);
            return $OXOResponse->jsonSerialize();
        }
        else {
            $user = new User();
      
            $user->first_name = $request->get('first_name');
            $user->last_name = $request->get('last_name');
            $user->email = $request->get('email');
            $user->faculty = $request->get('faculty');
            $user->department = $request->get('department');
            $user->mobile_number = $request->mobile_number;
            //$user->areas_of_research = $request->get('areas_of_research');

            $areas = [];

            if($request->has('areas_of_research_names'))
                $areas = $request->areas_of_research_names;

            if($request->has('area_1'))
            {
                $user->area_1 = $request->area_1;
                array_push($areas,$request->area_1);

            }
                
            if($request->has('area_2'))
            {
                $user->area_2 = $request->area_2;
                array_push($areas,$request->area_2);

            }

            if($request->has('area_3'))
            {
                $user->area_3 = $request->area_3;
                array_push($areas,$request->area_3);

            }


            if($request->has('from_postman')){

                $user->areas_of_research = '';

            } else {

                if(count($areas) > 1){

                    //if($request->has('areas_of_research_names'))
                        
                    $user->areas_of_research = implode(", ",$areas);
    
                } else if(count($areas) == 1){
    
                    $user->areas_of_research = $areas[0];
                }
            }

               
            $user->research_system_admin_admin = $request->get('research_system_admin_admin');
        
            $plainPassword = $request->input('password');
            $plainConfirmPassword = $request->input('password_confirm');

            $user->password = app('hash')->make($plainPassword);
            $user->password_confirm = app('hash')->make($plainConfirmPassword);

            if($request->has('middle_name'))
                $user->middle_name = $request->get('middle_name');

            if($request->has('research_system_admin_role'))
                $user->research_system_admin_role = $request->get('research_system_admin_role');

            if($request->has('other_areas'))
                $user->other_areas = $request->other_areas;

            if($user->save()):
                $OXOResponse = new \Oxoresponse\OXOResponse("User created successfully");
                $OXOResponse->setErrorCode(CoreErrors::OPERATION_SUCCESSFUL);
                $OXOResponse->setObject($user);

                if(!$request->has('research_system_admin_admin')){

                    foreach($request->areas_of_research as $area){

                        $area_user = new AreaUser();
            
                        $area_user->area_id = intval($area);
            
                        $area_user->user_id = $user->id;
            
                        $area_user->save();
                    }
                }
                

                return $OXOResponse->jsonSerialize();
            else:
                $OXOResponse = new \Oxoresponse\OXOResponse("Failed to create user. Kindly try again later");
                $OXOResponse->setErrorCode(CoreErrors::FAILED_TO_CREATE_RECORD);
                $OXOResponse->setObject($user);
                return $OXOResponse->jsonSerialize();
            endif;
        }
 
    }
}