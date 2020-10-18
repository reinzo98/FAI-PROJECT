<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function homePage(){
        if(Session::get("active")== null){
            $username = " ";
        }else{
            $username = Session::get("active");
        }
        return view('home');
    }
    
    public function insertNewUser(Request $req)
    {
        $validateData =[
            'username'=>'required ',
            'email'=>'required | unique:email_user ',            
            'pass'=>'required | min:8 ',
            'cpass'=>'required_with:pass | min:5 | same:cpass'
        ];
        $customMassage = [
            'required' => 'Form tidak boleh kosong..!!',            
            'same' => 'Password tidak sama..',
            'min' => 'Password minimal 8 karakter..',
            'unique' => 'Email sudah terdaftar..'
        ]; 
        $this->validate($req,$validateData,$customMassage);
        $nama = $req->username;
        $pass = $req->pass;
        $mail = $req->email;
        DB::insert('insert into users (username, email_user, password_user) values (?, ?, ?)', [$nama, $mail, $pass]);
        Session::put("active",$nama);
        return redirect('/');
    }
    
    public function loginCheck(Request $req){
        $validateData =[            
            'email'=>'required | unique:email_user ',            
            'pass'=>'required | min:8 '
        ];
        $customMassage = [
            'required' => 'Form tidak boleh kosong..!!',
            'min' => 'Password minimal 8 karakter..',
            'unique' => 'Email salah/ belum terdaftar..'
        ]; 
        $login = DB::select('select username from users where email_user = ?', [$req->email]);
        if(isset($login)){                
            $cekpass = DB::select('select password_user from users where email_user = ?', [$req->email]);
            if(isset($cekpass)){
                if($cekpass == $req->pass){
                    Session::put("active",$login);
                    return redirect('/');
                }
            }
        }else{
            
        }
    }
}
