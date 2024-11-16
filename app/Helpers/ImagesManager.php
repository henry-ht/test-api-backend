<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Iloveimg\CompressImageTask;
use ImgResize;

class ImagesManager{

    public static function publicSave($image, $nameFile, $folderName){

        $nameFile = $nameFile.'-'.Carbon::now()->format('u');

        Storage::putFileAs('public/'.$folderName, $image, $nameFile.'.'.$image->extension());

        return [
            'path'          => 'public/'.$folderName.'/'.$nameFile.'.'.$image->extension(),
            'public_path'   => 'storage/'.$folderName.'/'.$nameFile.'.'.$image->extension(),
        ];
    }

    public static function base64PublicSave($base64_image, $nameFile, $folderName){
        preg_match("/\/(.*?);/", $base64_image, $extension);

        $nameFile = $nameFile.'-'.Carbon::now()->format('u');
        Storage::disk('local')->put('public\\'.$folderName.'\\'.$nameFile.'.'.$extension[1], file_get_contents($base64_image));

        return [
            'path'          => $folderName.'/'.$nameFile.'.'.$extension[1],
            'public_path'   => 'storage/'.$folderName.'/'.$nameFile.'.'.$extension[1],
        ];
    }

    public static function base64PublicUpdate($base64_image, $nameFile, $folderName){

        Storage::disk('local')->put('public\\'.$folderName.'\\'.$nameFile, file_get_contents($base64_image));

        return true;
    }

    public static function deleteImg($nameFile, $folderName){
        Storage::disk('local')->delete('public/'.$folderName.'/'.$nameFile);
        return true;
    }

    public static function dimesions($filePatch){
        $imageData = Storage::disk('local')->get($filePatch);

        $width = ImgResize::make($imageData)->width(); // getting the image width
        $height = ImgResize::make($imageData)->height(); // getting the image height

        return [
            'width' => $width,
            'height' => $height,
        ];
    }

    public static function compress($pathFile) {
        $myTask = new CompressImageTask("project_public_36a0d40ff9fe9ff71fafff03cc9ea1ed_s9NI3ce8f84f11463d2c7b17767a58ff25dfc", "secret_key_10d269525978f980190ec338b67dcd69_O1ZVZ76e91abdfb63556ea90fc152200017f3");
        // $myTask = $iloveimg->newTask('compress');
        $file1 = $myTask->addFile($pathFile);
        $myTask->execute();
        $myTask->download($pathFile);
    }
}
