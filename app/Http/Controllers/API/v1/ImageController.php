<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\Request;
use League\Flysystem\Memory\MemoryAdapter;
use League\Glide\Responses\LaravelResponseFactory;
use League\Glide\ServerFactory;

class ImageController extends Controller
{
    public function showProfilePicture(FileSystem $fileSystem, $id)
    {
        if (file_exists(public_path('storage/images/profile/' . $id))) {
            $cacheFilesystem = new \League\Flysystem\Filesystem(new MemoryAdapter());

            $server = ServerFactory::create([
                'response' => new LaravelResponseFactory(app('request')),
                'source' => $fileSystem->getDriver(),
                'cache' => $cacheFilesystem,
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

    public function uploadProfilePicture(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $profileFicture = data_get($request->allFiles(), 'image', []);

        // Store picture, overwrite existing
        $path = 'app/public/images/profile';
        $filename = $request->id . '.' . $profileFicture->getClientOriginalExtension();
        $profileFicture->move(storage_path($path), $filename);

        response(200);
    }

    public function clearProfilePicture($id)
    {
        $image_path = public_path('storage/images/profile/' . $id . '.jpg');
        if (file_exists($image_path)) {
            unlink($image_path);
        }

        response(200);
    }
}
