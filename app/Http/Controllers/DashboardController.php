<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController 
{
    public function home(){
        return view('home');
    }
}
