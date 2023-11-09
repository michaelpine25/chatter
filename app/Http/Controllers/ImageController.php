<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function showProfilePicture(Request $request, $fileName) 
    {
        $path = storage_path('app/ProfilePictures/' . $fileName);

        return response()->file($path);
    }

    public function showConversationPhoto(Request $request, $fileName)
    {
        $path = storage_path('app/ConversationPhotos/' . $fileName);

        return response()->file($path);
    }

    public function showPictureMessage(Request $request, $fileName)
    {
        $path = storage_path('app/PictureMessages/' . $fileName);

        return response()->file($path);
    }
}
