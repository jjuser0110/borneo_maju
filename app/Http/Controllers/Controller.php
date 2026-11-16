<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Carbon\Carbon;
use App\Jobs\DoClosing;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    
    /** File types users may upload (receipts). Checked against the real file content, not the name. */
    public const ALLOWED_UPLOAD_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf'];

    public function upload($file, $module, $moduleId){
	    if (!empty($file)) {
            // Extension is taken from the detected MIME type, never from the user's filename,
            // so "shell.php" or "photo.jpg.php" can't end up as a runnable file.
            $extension = strtolower((string) $file->guessExtension());
            if (!in_array($extension, self::ALLOWED_UPLOAD_EXTENSIONS, true)) {
                abort(422, 'Only image or PDF files can be uploaded.');
            }

            $fileNameToStore = \Illuminate\Support\Str::random(40).'.'.$extension;

            $latest_filename = (int) $moduleId."/".$fileNameToStore;

            $path = $file->storeAs($module,$latest_filename,'public');

            return [
                'file_name' => $fileNameToStore,
                'file_path' => $path,
                'file_type' => $extension
            ];
	    }
    }

}
