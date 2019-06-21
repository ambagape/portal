<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\Request;
use League\Glide\Responses\LaravelResponseFactory;
use League\Glide\ServerFactory;

class ImageController extends Controller
{
    public function showProfilePicture(FileSystem $fileSystem, $id)
    {
        if (file_exists(public_path('storage/images/profile/' . $id))) {
            $server = ServerFactory::create([
                'response' => new LaravelResponseFactory(app('request')),
                'source' => $fileSystem->getDriver(),
                'cache' => $fileSystem->getDriver(),
                'source_path_prefix' => '/public',
                'cache_path_prefix' => '/public/.cache',
                'base_url' => 'img',
            ]);

            return $server->getImageResponse('images/profile/' . $id, request()->all());

        // Return placeholder if no picture is found
        } else {
            $server = ServerFactory::create([
                'response' => new LaravelResponseFactory(app('request')),
                'source' => public_path('images'),
                'cache' => public_path('images'),
                'cache_path_prefix' => '/.cache',
                'base_url' => 'img',
            ]);

            return $server->getImageResponse('profilePlaceholder.png', request()->all());
        }
    }

    public function uploadProfilePicture(FileSystem $fileSystem, Request $request)
    {
        $request->validate([
            'id' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $profileFicture = data_get($request->allFiles(), "image", []);

        // Store picture, overwrite existing
        $path = 'app/public/images/profile';
        $filename = $request->id . '.' . $profileFicture->getClientOriginalExtension();
        $profileFicture->move(storage_path($path), $filename);

        // Clear glide cache
        $server = ServerFactory::create([
            'response' => new LaravelResponseFactory(app('request')),
            'source' => $fileSystem->getDriver(),
            'cache' => $fileSystem->getDriver(),
            'source_path_prefix' => '/public',
            'cache_path_prefix' => '/public/.cache',
            'base_url' => 'img',
        ]);

        $server->deleteCache($filename);

        response(200);
    }
}
