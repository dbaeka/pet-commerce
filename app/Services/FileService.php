<?php

namespace App\Services;

use App\Models\File;
use App\Repositories\Interfaces\FileRepositoryContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Spatie\LaravelData\Data;
use Str;

readonly class FileService
{
    private FileRepositoryContract $file_repository;

    public function __construct()
    {
        $this->file_repository = app(FileRepositoryContract::class);
    }

    /**
     * @param UploadedFile $file
     * @return Data|Builder<File>|Model|null
     */
    public function saveFile(UploadedFile $file): Model|Builder|Data|null
    {
        $dir = storage_path(config('app.pet_shop_file_dir'));
        $file_name = sha1(Str::random()) . '.' . $file->getClientOriginalExtension();
        $saved_file = $file->move($dir, $file_name);
        $relative_path = Str::after($saved_file->getRealPath(), storage_path());
        $data = [
            'name' => Str::random(20),
            'path' => $relative_path,
            'size' => $saved_file->getSize(),
            'type' => $file->getClientMimeType(),
        ];
        return $this->file_repository->create($data);
    }
}
