<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StaticPagesController extends Controller
{

    public function term_condition()
    {
        $type = 'static'; // Example static type
        $lname = 'Add location';
        $checkin = 'Add date';
        $checkinDate = null;
        $checkoutDate = null;
        $guest = 'Add guests';
        $rooms = '';
        $slugid = null;
        $slug = null;
        $Username = null;
        $sightImages = collect();
    
        // Check if user is logged in
        if (session()->has('frontend_user')) {
            $userData = session('frontend_user');
            $Username = $userData['Username'] ?? null;
        }
        return view('term_condition')->with([
            'type' => $type,
            'lname' => $lname,
            'checkin' => $checkin,
            'checkinDate' => $checkinDate,
            'checkoutDate' => $checkoutDate,
            'guest' => $guest,
            'rooms' => $rooms,
            'slugid' => $slugid,
            'slug' => $slug,
            'Username' => $Username,
            'sightImages' => $sightImages,
        ]);
    }

    public function about_us()
    {
        $type = 'static'; // Example static type
        $lname = 'Add location';
        $checkin = 'Add date';
        $checkinDate = null;
        $checkoutDate = null;
        $guest = 'Add guests';
        $rooms = '';
        $slugid = null;
        $slug = null;
        $Username = null;
        $sightImages = collect();
    
        // Check if user is logged in
        if (session()->has('frontend_user')) {
            $userData = session('frontend_user');
            $Username = $userData['Username'] ?? null;
        }
        return view('about_us')->with([
            'type' => $type,
            'lname' => $lname,
            'checkin' => $checkin,
            'checkinDate' => $checkinDate,
            'checkoutDate' => $checkoutDate,
            'guest' => $guest,
            'rooms' => $rooms,
            'slugid' => $slugid,
            'slug' => $slug,
            'Username' => $Username,
            'sightImages' => $sightImages,
        ]);
    }

    public function career()
    {
        $type = 'static'; // Example static type
        $lname = 'Add location';
        $checkin = 'Add date';
        $checkinDate = null;
        $checkoutDate = null;
        $guest = 'Add guests';
        $rooms = '';
        $slugid = null;
        $slug = null;
        $Username = null;
        $sightImages = collect();
    
        // Check if user is logged in
        if (session()->has('frontend_user')) {
            $userData = session('frontend_user');
            $Username = $userData['Username'] ?? null;
        }
        return view('career')->with([
            'type' => $type,
            'lname' => $lname,
            'checkin' => $checkin,
            'checkinDate' => $checkinDate,
            'checkoutDate' => $checkoutDate,
            'guest' => $guest,
            'rooms' => $rooms,
            'slugid' => $slugid,
            'slug' => $slug,
            'Username' => $Username,
            'sightImages' => $sightImages,
        ]);
    }

    public function contact_us()
    {
        $type = 'static'; // Example static type
        $lname = 'Add location';
        $checkin = 'Add date';
        $checkinDate = null;
        $checkoutDate = null;
        $guest = 'Add guests';
        $rooms = '';
        $slugid = null;
        $slug = null;
        $Username = null;
        $sightImages = collect();
    
        // Check if user is logged in
        if (session()->has('frontend_user')) {
            $userData = session('frontend_user');
            $Username = $userData['Username'] ?? null;
        }
        return view('contact_us')->with([
            'type' => $type,
            'lname' => $lname,
            'checkin' => $checkin,
            'checkinDate' => $checkinDate,
            'checkoutDate' => $checkoutDate,
            'guest' => $guest,
            'rooms' => $rooms,
            'slugid' => $slugid,
            'slug' => $slug,
            'Username' => $Username,
            'sightImages' => $sightImages,
        ]);
    }

    public function privacy_policy()
    {
        $type = 'static'; // Example static type
        $lname = 'Add location';
        $checkin = 'Add date';
        $checkinDate = null;
        $checkoutDate = null;
        $guest = 'Add guests';
        $rooms = '';
        $slugid = null;
        $slug = null;
        $Username = null;
        $sightImages = collect();
    
        // Check if user is logged in
        if (session()->has('frontend_user')) {
            $userData = session('frontend_user');
            $Username = $userData['Username'] ?? null;
        }
        return view('privacy_policy')->with([
            'type' => $type,
            'lname' => $lname,
            'checkin' => $checkin,
            'checkinDate' => $checkinDate,
            'checkoutDate' => $checkoutDate,
            'guest' => $guest,
            'rooms' => $rooms,
            'slugid' => $slugid,
            'slug' => $slug,
            'Username' => $Username,
            'sightImages' => $sightImages,
        ]);
    }

    public function trust_and_safety()
    {
        $type = 'static'; // Example static type
        $lname = 'Add location';
        $checkin = 'Add date';
        $checkinDate = null;
        $checkoutDate = null;
        $guest = 'Add guests';
        $rooms = '';
        $slugid = null;
        $slug = null;
        $Username = null;
        $sightImages = collect();
    
        // Check if user is logged in
        if (session()->has('frontend_user')) {
            $userData = session('frontend_user');
            $Username = $userData['Username'] ?? null;
        }
        return view('trust_and_safety')->with([
            'type' => $type,
            'lname' => $lname,
            'checkin' => $checkin,
            'checkinDate' => $checkinDate,
            'checkoutDate' => $checkoutDate,
            'guest' => $guest,
            'rooms' => $rooms,
            'slugid' => $slugid,
            'slug' => $slug,
            'Username' => $Username,
            'sightImages' => $sightImages,
        ]);
    }

    public function accessibility_statement()
    {
        $type = 'static'; // Example static type
        $lname = 'Add location';
        $checkin = 'Add date';
        $checkinDate = null;
        $checkoutDate = null;
        $guest = 'Add guests';
        $rooms = '';
        $slugid = null;
        $slug = null;
        $Username = null;
        $sightImages = collect();
    
        // Check if user is logged in
        if (session()->has('frontend_user')) {
            $userData = session('frontend_user');
            $Username = $userData['Username'] ?? null;
        }
        return view('accessibility_statement')->with([
            'type' => $type,
            'lname' => $lname,
            'checkin' => $checkin,
            'checkinDate' => $checkinDate,
            'checkoutDate' => $checkoutDate,
            'guest' => $guest,
            'rooms' => $rooms,
            'slugid' => $slugid,
            'slug' => $slug,
            'Username' => $Username,
            'sightImages' => $sightImages,
        ]);
    }
}
