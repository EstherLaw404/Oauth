<?php namespace App\Http\Controllers\API;

use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Auth;
use Validator;
use DB;
use URL;

use App\Http\Controllers\Controller; 

use App\User; 
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class OAuthController extends Controller 
{
    public function __construct(){
     
    }

    public $successStatus = 200;

    /** 
     * api signUp 
     * @return \Illuminate\Http\Response 
     */ 
   public function signUp(Request $request){
        $data = array();
 

         if(empty($rules)){
            $rules = [
            /* oauth */  
            'client_id' => 'required|exists:oauth_clients,id', 
            'secret' => 'required|exists:oauth_clients,secret', 
            'scope' => '', 
            'name' => 'required', 
            'email' => 'required', 
            'password' => 'required|min:6',
            'c_password' => 'required_with:password||same:password', 
            'grant_type' => 'required|in:password',
           
        ];
        }
        
        $validator = Validator::make($request->all(), $rules);  
        if ($validator->fails()) { 
            return Response(['statusCode'=>401,'status'=>'error','message'=>$validator->errors()->all()[0],'data'=>$request->all() ], 401);
        }

        $users = new User;
       // $users->id = $users_id;
        $users->name = $request->name;
        $users->email = $request->email;
        $users->password = bcrypt($request->password);
        $users->save();

        /* Validate User & Password then get token */
        //Passport::tokensExpireIn(Carbon::now()->addMinutes($expires_min));
        $http = new Client();
        $response = $http->post( URL::to("/oauth/token"), [
            'form_params' => [
                'grant_type' => $request->grant_type,
                'client_id' => $request->client_id,
                'client_secret' => $request->secret,
                'username' => $request->email,
                'password' => $request->password,
                'scope' => "",
            ],
        ]);
        $ret_data =  json_decode((string) $response->getBody(), true);
        print_r($ret_data);exit();
        return ['statusCode'=>200,'status'=>'success','message'=>'success_register_pleases_check_your_email','data'=>$ret_data];
    }## function 

        /** 
     * api signIn
     * @return \Illuminate\Http\Response 
     */ 
    public function signIn(Request $request){

         $data = array();

         $validator = Validator::make($request->all(), [ 
            'client_id' => 'required|exists:oauth_clients,id', 
            'secret' => 'required|exists:oauth_clients,secret', 
            'scope' => '', 
            'email' => 'required', 
            'password' => 'required|min:6',
            'grant_type' => 'required|in:password',
        ]);
    
        if ($validator->fails()) { 
            return Response(['statusCode'=>401,'status'=>'error','message'=>$validator->errors()->all()[0],'data'=>$request->all() ], 401);
        }
      
        $http = new Client();
        $response = $http->post( URL::to("/oauth/token"), [
            'form_params' => [
                'grant_type' => $request->grant_type,
                'client_id' => $request->client_id,
                'client_secret' => $request->secret,
                'username' => $request->email,
                'password' => $request->password,
                'scope' => "",
            ],
        ]);
        $ret_data =  json_decode((string) $response->getBody(), true);
       
        
    return ['statusCode'=>200,'status'=>'success','message'=>'success_login','data'=>$ret_data];
 
    }## [END] function 
      /** 
     * api refreshToken 
     * @return \Illuminate\Http\Response 
     */ 
    public function refreshToken(Request $request){
         $data = array();
         
         $validator = Validator::make($request->all(), [ 
          
            'client_id' => 'required|exists:oauth_clients,id', 
            'secret' => 'required|exists:oauth_clients,secret', 
            'refresh_token' => 'required', 
        ]);
        
        try {
            /* Validate User & Password then get token */
            $http = new Client;
            $response = $http->post(URL::to("/oauth/token"), [
                'form_params' => [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $request->refresh_token,
                    'client_id' => $request->client_id,
                    'client_secret' => $request->secret,
                    'scope' => '',
                ],
            ]);

            $ret_data =  json_decode((string) $response->getBody(), true);
            $data = $ret_data;

            return ['statusCode'=>200,'status'=>'success','message'=>array('success_refresh_token'),'data'=>$data];

        } catch(ClientException $e) {
            $response = $e->getResponse();
            $responseBody = json_decode($response->getBody()->getContents());
            $message = (!empty($responseBody->message))?$responseBody->message:"Error";
            $message .= (!empty($responseBody->hint))?" (".$responseBody->hint.")":"";

            return ['statusCode'=>404,'status'=>'error','message'=>array($message),'data'=>$data];
        }
        
    }## [END] function 


      public function signOut(Request $request){
        $data = array();
        
        $users = $request->user();
        
        $bearerToken_val = $request->bearerToken();
        
        if (!empty($bearerToken_val)) {
            
            $request->user()->token()->revoke();
            
            return ['statusCode'=>200,'status'=>'success','message'=>'success_logout','data'=>$data];
        }else{
            return ['statusCode'=>401,'status'=>'error','message'=>'error_token_logout_failure','data'=>$data];
        }
        
    }## [END] function
    
} ## class 
