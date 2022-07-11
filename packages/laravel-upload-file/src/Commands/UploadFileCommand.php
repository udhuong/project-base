<?php

namespace Udhuong\LaravelUploadFile\Commands;

use Illuminate\Console\Command;

class UploadFileCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upload:file';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $uploaded = app('upload_file.uploader')
            ->fromSource(public_path('imgs/btn-menu.png'))
            ->toDestination('s3', 'uploads')
            ->upload();
        dd($uploaded);
    }
}
