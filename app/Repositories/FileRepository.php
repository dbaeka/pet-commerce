<?php

namespace App\Repositories;

use App\Dtos\File as FileDto;
use App\Models\File;
use App\Repositories\Interfaces\FileRepositoryContract;

/**
 * @extends BaseCrudRepository<File, FileDto>
 */
class FileRepository extends BaseCrudRepository implements FileRepositoryContract
{
}
