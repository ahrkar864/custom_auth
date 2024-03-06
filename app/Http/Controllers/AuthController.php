<?php

namespace App\Http\Controllers;

use Mail;
use Carbon\Carbon;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\EmailVerificationMail;

class AuthController extends Controller
{
    public function getRegister(){
        return view('auth.register');
    }

    public function check_email_unique(Request $request){
    	$user=User::where('email',$request->email)->first();
    	if($user){
    		echo 'false';
    	}else{
    		echo 'true';
    	}
    }

    public function postRegister(Request $request){
        $request->validate([
        'first_name'=>'required|min:2|max:100',
        'last_name'=>'required|min:2|max:100',
        'email'=>'required|email|unique:users',
        'password'=>'required|min:6|max:100',
        'confirm_password'=>'required|same:password',
        'terms'=>'required',
        'grecaptcha'=>'required'
        ],[
           'first_name.required'=>'First name is required',
           'last_name.required'=>'Last name is required',
        ]);

        $grecaptcha=$request->grecaptcha;

        $client = new Client();

        $response = $client->post(
            'https://www.google.com/recaptcha/api/siteverify',
            ['form_params'=>
                [
                    'secret'=>env('GOOGLE_CAPTCHA_SECRET'),
                    'response'=>$grecaptcha
                ]
            ]
        );
     
        $body = json_decode((string)$response->getBody());

        if($body->success==true){

            $user=User::create([
                'first_name'=>$request->first_name,
                'last_name'=>$request->last_name,
                'email'=>$request->email,
                'password'=>bcrypt($request->password),
                'email_verification_code'=>Str::random(40)
            ]);

            Mail::to($request->email)->send(new EmailVerificationMail($user));

            return redirect()->back()->with('success','Registration successfull.Please check your email address for email verification link.');
            
        }else{
            return redirect()->back()->with('error','Invalid recaptcha');
        }
    }

    public function verify_email($verification_code){
        $user=User::where('email_verification_code',$verification_code)->first();
        if(!$user){
            return redirect()->route('getRegister')->with('error','Invalid URL');
        }else{
            if($user->email_verified_at){
                return redirect()->route('getRegister')->with('error','Email already verified');
            }else{
                $user->update([
                    'email_verified_at'=>Carbon::now()
                ]);
                return redirect()->route('getRegister')->with('success','Email successfully verified');
            }
        }
    }

    public function getlogin() {
        return view('auth.login');
    }

    public function postlogin(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6|max:100',
            'grecaptcha' => 'required'
        ]);

        $grecaptcha=$request->grecaptcha;

        $client = new Client();

        $response = $client->post(
            'https://www.google.com/recaptcha/api/siteverify',
            ['form_params'=>
                [
                    'secret'=>env('GOOGLE_CAPTCHA_SECRET'),
                    'response'=>$grecaptcha
                ]
            ]
        );

        $body = json_decode((string)$response->getBody());

        if($body->success==true){
            $user=User::where('email',$request->email)->first();

            if(!$user){
                return redirect()->back()->with('error','Email is not registered');
            }else{

                if(!$user->email_verified_at){
                    return redirect()->back()->with('error','Email is not verified');
                }else{

                    if(!$user->is_active){
                        return redirect()->back()->with('error','User is not active. Contact admin');
                    }else{

                        $remember_me=($request->remember_me)?true:false;
                        if(auth()->attempt($request->only('email','password'),$remember_me)){

                            // dd('login successful');
                            return redirect()->route('dashboard')->with('success','Login successfull');

                        }else{
                            return redirect()->back()->with('error','Invalid credentials');
                        }

                    }

                }

            }

        } else {
            return redirect()->back()->with('error','Invalid recaptcha');
        }
    }   

    public function logout() {
        auth()->logout();
        return redirect()->route('getlogin')->with('success','Logout successful');
    }
}
