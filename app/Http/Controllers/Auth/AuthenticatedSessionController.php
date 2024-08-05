<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Laravel\Socialite\Facades\Socialite;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }
    

    public function otp($otpCode="",$email=""): View
    {
        $data['otpcode'] = (($otpCode=="" ) ? "No OTP Was Generated" : $otpCode );
        $data['email'] = (($email=="" ) ? "" : base64_encode($email) );

        return view('auth.otp-page',$data);
    }


    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();
// 
        return redirect()->intended(route('dashboard', absolute: false));
    }


    public function googleredirect($driver)
    {
    return Socialite::driver($driver)->redirect();
    }

    public function storegoogleusers($driver){

        $response = array();

        try {
            $user = Socialite::driver($driver)->user();
        } catch (\Exception $e) {
            return redirect()->route('login');
        }
       
        $existingUser = User::where('email', $user->getEmail())->first();

        if ($existingUser) {
          //  return redirect()->route('login');
         auth()->login($existingUser, true);
        }else{
            $createUsers = User::create([
                "provider_name"=> $driver,
                "provider_id"=> $user->getId(),
                "name"=> $user->getName(),
                "email"=> $user->getEmail(),
                "email_verified_at"=> now(),
                "avatar" =>$user->getAvatar(),     
            ]);
          //  dd($createUsers);
           // auth()->login($createUsers, true);

           if($createUsers){
              $noewuser=  $this->createotp($user->getName());
                $email = $user->getEmail();
               // dd( $noewuser);
                  return $this->otp(base64_encode($noewuser),$email );
            } else {
                $response['messege'] = "Users was not created try again";
                $response['status'] = "failed";
                return response()->json($response);
            }
        }

        return  redirect()->route('login');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

 public function createN($email,$otp){
   $response= array();
    $confirm_OTP_update = Otp::where('code', '=', $otp)
    ->limit(1)
    ->orderBy('id', 'desc')
    ->update([
        "status" => "Y"
    ]);
            //  dd( $confirm_OTP_update);
        if ($confirm_OTP_update) {
            $otp_ = rand(100000, 999999);
            $createotp = Otp::create([
                'email' => $email,
                'code' => $otp_
            ]);
          //  dd($createotp);
            if ($createotp) {
                $response['messege'] = $createotp->code;
                $response['status'] = "success";
            
            } else {
                $response['messege'] = "";
                $response['status'] = "failed";
            }
            return response()->json($response);
        }
      
 }
    public function createotp($email, $otp = "")
    {
        $otp = rand(100000, 999999);

        $confirmOTP = Otp::where('email', $email)
            ->where('code', '=', $otp)
            // ->where('status','=','N')
            ->limit(1)
            ->orderBy('id', 'desc')
            ->first();

        if (is_null($confirmOTP)) {
            $createotp = Otp::create([
                'email' => $email,
                'code' => $otp
            ]);

            if ($createotp) {

               return $createotp->code;
           
            } else {

                return false;
            }
        } elseif ($confirmOTP->status = "N") {

            $confirm_OTP_update = Otp::where('code', '=', $otp)
                ->limit(1)
                ->orderBy('id', 'desc')
                ->update([
                    "status" => "Y"
                ]);

            if ($confirm_OTP_update) {

                $createotp = Otp::create([
                    'email' => $email,
                    'code' => $otp
                ]);

                if ($createotp) {
                    $response['messege'] = "Otp Created";
                    $response['messege'] = $createotp->code;
                    $response['status'] = "success";
                    return response()->json($response);
                } else {
                    return false;
                }
            }
        }
    }


    public function updateotp($email,$otp)
    {
        $response = array();
       
        $confirm_OTP = Otp::where('code','=',$otp)
        ->limit(1)
       ->orderBy('id', 'desc')
       ->first();
       // dd($confirm_OTP);
        if($confirm_OTP->status == "N" ){

            $confirm_OTP_update = Otp::where('code','=',$otp)
            ->limit(1)
           ->orderBy('id', 'desc')
           ->update([
            "status"=>"Y"
           ]);
          // dd($confirm_OTP_update );
            if ($confirm_OTP_update) {
               
                $response['status'] = "success";
            }else{
               
                $response['status'] = "failed";
         }
        }else{
            return false;
        }

        return response()->json($response);
    }
    

    public function Otpconfirmation(request $request)
    {
       // dd($request->otp);
       $response = array();
       $request->validate([
            'email' => '',
            'otp' => '',    
        ]); 

        
        if (is_null($request->otp)) {
                 $response['message'] = "Please Enter your OTP Code for Confirmation";
                 $response['status'] = "failed";
        } else {

            $confirmOTP = Otp::where('code','=',$request->otp)
                                       // ->where('status','=','N')
                                        ->limit(1)
                                       ->orderBy('id', 'desc')
                                       ->first();
       // dd($confirmOTP->code);
                if(is_null($confirmOTP)){

                    $response['message'] = "Please Enter your OTP Code for Confirmation";
                    $response['status'] = "failed";
                 
                } elseif ($confirmOTP->code !== $request->otp) {

                    $response['message'] = "Please Check your OTP Code";
                    $response['status'] = "failed";
                  
                }elseif($confirmOTP->code == $request->otp){
                
                

                    $updateCode = Otp::where('code', '=', $request->otp)->Update([
                        'status' => "Y"
                    ]);
             //   dd($updateCode);
                
                    if($updateCode == true){
                       
                        $existingUser = User::where('email', $request->email)->first();
                        $host = 'http://'.request()->getHttpHost().'/dashboard';
                    
                                            $response['status'] = "success";
                    $response['route'] = $host;
                        auth()->login($existingUser, true);
                    
                    
                    } else {
                    
                    }
                    
                }
       
        }
        return response()->json($response);
     }

    
}
