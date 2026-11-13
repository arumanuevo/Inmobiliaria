<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ImageController extends Controller
{
    public function list()
    {
       
        $images = File::files(public_path('images/portada'));
        $images = array_map(function($file) {
            return basename($file);
        }, $images);

        return response()->json($images);
    }
}

