<?php

namespace App\Services;

use App\Dtos\BaseDto;
use App\Models\File;
use App\Repositories\FileRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Str;

readonly class FileService
{
    private FileRepository $file_repository;

    public function __construct()
    {
        $this->file_repository = app(FileRepository::class);
    }

    /**
     * @param UploadedFile $file
     * @return BaseDto|Builder<File>|Model|null
     */
    public function saveFile(UploadedFile $file): Model|Builder|BaseDto|null
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
