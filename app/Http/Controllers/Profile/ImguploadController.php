<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateProfilepicRequest;
use Illuminate\Support\Facades\Storage;


class ImguploadController extends Controller
{
    public function update(UpdateProfilepicRequest $request){
        
       // dd($request->all()); //input('_token'));

        //$path = $request->file('profilepic')->store('profilepictures','public');
        $path = Storage::disk('public')->put('profilepictures', $request->file('profilepic'));
        //dd($path);
        if($oldProfilePic = $request->user()->profilepic ){
            Storage::disk('public')->delete($oldProfilePic);
           //dd($oldProfilePic);
        }
        
        auth()->user()->update(['profilepic'=> $path]);
        //Save Image method
        return response()->redirectTo(route('profile.edit'))->with('message','Profile Picture updated successfully.');        
    }
}
