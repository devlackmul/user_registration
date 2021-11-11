<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Routing\UrlGenerator;
class RegistrationController extends Controller
{
    function adminLogin(Request $request)
    {
        $user_name = $request['user_name'];
        $password = $request['password']; 
        $user_info = User::where('user_name', $user_name)->where('password', $password)->first();
        if(!empty($user_info))
        {
            $user_id = $user_info['id'];
            $user_name = $user_info['user_name'];
            $user_password = $user_info['password'];
            return "Admin Loggedin. Admin User Id is : ".$user_id." ## Admin Username is : ".$user_name." ## Admin Password is : ".$user_password ;
        }
        else{
            return "User not found";
        }
    }
    function onInvite(Request $request)
    {
        $email = $request['user_email'];
        $user_name = $request['user_name'];
        $id = $request['admin_id'];
        $user_info = User::where('id',$id)->first();
        $user_role = $user_info->user_role;
        if($user_role=="admin")
        {
            $subject = 'Request For Registration';
            //$registrationLink = "http://localhost:8005/signup";
           
            $registrationLink = request()->root();
            $registrationLink = $registrationLink.'/signup' ;
            $data = array('name'=>$user_name,'registrationLink'=>$registrationLink);
            Mail::send('mailforinvitation', $data, function($message) use ($email,$subject,$user_name)  {
            $message->to($email,  $user_name)->subject($subject);
            $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
            });
            return "Mail sent successfully";
        }
        else{
            return "Not enough privilege to send this mail";
        }


    }
    function onRegister(Request $request)
    {
        $otp = rand(100000, 999999); 
        $user_name = $request['user_name'];
        $length_of_username = strlen($user_name);
        if($length_of_username < 4 || $length_of_username > 20)
        {
            return "Length of Username must be between 4 and 20";
        }
        $email = $request['email'];
        $password = $request['password'];
        $userCount = User::where('user_name',$user_name)->count();
        if($userCount!=0)
        {
            return "User already exists";
        }
        else{
           $result =  User::insert([
                'user_name'=>$user_name,
                'email'=>$email,
                'password'=>$password,
                'otp'=>$otp,
                'user_role'=>'user',
            ]);
            if($result == true)
            {  
                $subject = 'OTP for Registration';
                $data = array('name'=>$user_name,'otp'=>$otp);
                Mail::send('mail', $data, function($message) use ($email,$subject,$user_name)  {
                $message->to($email,  $user_name)->subject($subject);
                $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
                });
                return "OTP has been sent to your Mail Address. Use this OTP to finalize your registration process.";
            }
            else{
                return "Registration Failed";
            }
        }

    }

    function onOtpUpdate(Request $request)
    {
        $otp = $request['otp']; 
        $otpCount = User::where('otp',$otp)->count();
        if($otpCount!=0 )
        {
            $savedOtp = User::where('otp',$otp)->first();
            $savedOtp->registered_at = Carbon::now()->toDateTimeString();
            $savedOtp->save();
            return "Registered Successfully";
        }
        else{
                return "Wrong OTP ".$otp;
            }
    }

    function userLogin(Request $request)
    {
        $user_name = $request['user_name'];
        $password = $request['password']; 
        $user_info = User::where('user_name', $user_name)->where('password', $password)->first();
        if(!empty($user_info))
        {
            $user_id = $user_info['id'];
            $user_name = $user_info['user_name'];
            $user_password = $user_info['password'];
            return "User Loggedin. User Id is : ".$user_id." ## Username is : ".$user_name." ## User Password is : ".$user_password ;
        }
        else{
            return "User not found";
        }
    }

    function profileUpdate(Request $request)
    {
        $id = $request['id'];
        $user_name = $request['user_name'];
        $length_of_username = strlen($user_name);
        if($length_of_username < 4 || $length_of_username > 20)
        {
            return "Length of Username must be between 4 and 20";
        }
        $password = $request['password']; 
        $name = $request['name'];
        $avatar = $request['avatar'];
        $avatarSize = getimagesize($avatar);
        $avatarWidth = $avatarSize[0];
        $avatarHeight = $avatarSize[1];
        if($avatarWidth != 256 && $avatarHeight != 256)
        {
            return "Image size/dimension must be  256px x 256px ";
        }
        $email = $request['email'];

        $user_info = User::where('id',$id)
                            ->update([
                                'name' => $name,
                                'avatar' => $avatar,
                                'email' => $email,
                                'user_name' => $user_name,
                                'password' => $password
                            ]);
        if($user_info!=0)
        {
            return "Profile Updated Successfully";
        }
        else
        {
            return "Update Failed";
        }
        
    }

       

}
