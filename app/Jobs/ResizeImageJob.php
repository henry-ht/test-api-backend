<?php

namespace App\Jobs;

use App\Helpers\ImagesManager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use ImgResize;

class ResizeImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $path;
    public $replace;
    public $width;
    public $height;
    public $folderSize;
    public $withCanvas;


    /**
     * Create a new job instance.
     */
    public function __construct(string $path, string $replace, int|null $width, int|null $height, string $folderSize = null, $withCanvas = true)
    {
        $this->path         = $path;
        $this->replace      = $replace;
        $this->width        = $width;
        $this->height       = $height;
        $this->folderSize   = $folderSize;
        $this->withCanvas   = $withCanvas;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if(isset($this->folderSize)){
            if(!File::isDirectory($this->folderSize)){
                File::makeDirectory($this->folderSize, 0777, true, true);
            }
        }

        $imgResize = ImgResize::make($this->path);

        // $imgResize->encode('jpg', 75);

        if(isset($imgResize->height) || isset($imgResize->width)){
            $imgResize->resize($this->width, $this->height, function ($constraint) {
                $constraint->aspectRatio();
                // $constraint->upsize();
            });
        }

        if ($this->withCanvas ) {
            $imgResize->resizeCanvas($this->width, $this->height, 'center', false, 'fff');
        }


        $imgResize->save(($this->replace ?? $this->path), 80, 'jpg');

        // ImagesManager::compress($this->path);
    }
}
