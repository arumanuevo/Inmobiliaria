<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Inmueble;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function adminView()
    {
        $users = User::all();
        $users = User::paginate(10);
        return view('inmuebles.show', compact('users'));
        
    }
}