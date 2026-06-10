<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('storage:check-supabase', function () {
    $disk = 'supabase';
    $config = config("filesystems.disks.{$disk}");
    $required = ['key', 'secret', 'bucket', 'endpoint', 'region'];

    foreach ($required as $key) {
        if (blank($config[$key] ?? null)) {
            $this->error("Missing Supabase storage config: {$key}");
            return 1;
        }
    }

    $path = 'diagnostics/'.now()->format('YmdHis').'-storage-check.txt';

    try {
        Storage::disk($disk)->put($path, 'supabase-storage-ok', 'public');

        if (!Storage::disk($disk)->exists($path)) {
            $this->error('Upload finished, but the file was not found in the bucket.');
            return 1;
        }

        $this->info('Supabase Storage upload check succeeded.');
        $this->line('Bucket: '.$config['bucket']);
        $this->line('Endpoint: '.$config['endpoint']);
        $this->line('Public URL: '.Storage::disk($disk)->url($path));

        Storage::disk($disk)->delete($path);

        return 0;
    } catch (\Throwable $exception) {
        $this->error('Supabase Storage upload check failed.');
        $this->line($exception->getMessage());

        return 1;
    }
})->purpose('Verify Supabase Storage S3 upload, public URL, and delete access');
