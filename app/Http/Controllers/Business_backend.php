<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 

class Business_backend extends Controller
{
   public function busi_index(){
    $getuser = DB::table('business_detail as b')
    ->join('business_user as u','b.userid','=','u.id')
    ->select('b.*','u.username','u.id as uid','u.busi_username')
    ->orderby('b.id','asc')->get(); 
   //
    return view('backend.business.business_index',['getuser'=>$getuser]);
   }
  

   public function all_busi_users(){
    $getuser = DB::table('business_user')->orderby('id','asc')->get(); 
    return view('backend.business.all_business_users',['getuser'=>$getuser]);
  }
  
  public function delete_business_users(request $request){
    $id = $request->get('id');
    DB::table('business_user')->where('id',$id)->delete();

    $getuser = DB::table('business_user')->orderby('id','asc')->get();
    return view('backend.business.filter_all_users',['getuser'=>$getuser]);
}


public function business_active(request $request){
  $value =  $request->get('value');
  $id = $request->get('id');
  $data = array(
     'IsEmailVerify'=>$value,           
  );
  DB::table('business_user')->where('id', $id)->update($data);

  $getuser = DB::table('business_user')->orderby('id','asc')->get();
  return view('backend.business.filter_all_users',['getuser'=>$getuser]);

}

public function business_status_active(request $request){
  $value =  $request->get('value');
  $id = $request->get('id');
  $data = array(
     'varify_business'=>$value,           
  );
  DB::table('business_detail')->where('id', $id)->update($data);

  $getuser = DB::table('business_detail as b')
 
    ->join('business_user as u','b.userid','=','u.id')
    ->select('b.*','u.username','u.id as uid','u.busi_username')
    ->orderby('b.id','asc')->get(); 
  return view('backend.business.updated_business',['getuser'=>$getuser]);

}


public function delete_business(request $request){
  $id = $request->get('id');
  DB::table('business_detail')->where('id',$id)->delete();

  $getuser = DB::table('business_detail as b')
  ->select('b.*','u.username','u.id as uid','u.busi_username')
    ->orderby('b.id','asc')->get(); 
  return view('backend.business.updated_business',['getuser'=>$getuser]);
}
}
