<?php

namespace Cheney\AdminSystem\Controllers;

use Cheney\AdminSystem\Controllers\Controller;
use Illuminate\Http\Request;
use Cheney\AdminSystem\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    use ApiResponseTrait;

    public function upload(Request $request)
    {
        try {
            $validated = $request->validate([
                'file' => 'required|image|max:2048|mimes:jpeg,png,gif'
            ]);

            $file = $validated['file'];
            $path = $file->store('avatars', 'public');
            
            return $this->success([
                'url' => env('APP_URL').Storage::url($path),
                'path' => $path
            ], '上传成功');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
