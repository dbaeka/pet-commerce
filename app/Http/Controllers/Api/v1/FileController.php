<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\File\StoreFileRequest;
use App\Http\Resources\v1\BaseResource;
use App\Models\File;
use App\Repositories\Interfaces\FileRepositoryContract;
use App\Services\FileService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * @OA\Tag(
 *     name="File",
 *     description="File API endpoint"
 * )
 */
class FileController extends Controller
{
    public function __construct(
        private readonly FileRepositoryContract $file_repository
    ) {
        $this->middleware('secure')->except(['show']);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/files/upload",
     *     operationId="files-upload",
     *     summary="Upload a file",
     *     tags={"File"},
     *     @OA\RequestBody(
     *      required=true,
     *      @OA\MediaType(
     *       mediaType="multipart/form-data",
     *       @OA\Schema(
     *        ref="#/components/schemas/StoreFileRequest"
     *       )
     *      )
     *     ),
     *     @OA\Response(response=201, ref="#/components/responses/Created"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/Unprocessable"),
     *     @OA\Response(response=500, ref="#/components/responses/ServerError")
     * )
     */
    public function store(StoreFileRequest $request): BaseResource
    {
        $file = $request->validated()['file'];
        $saved_file = (new FileService())->saveFile($file);
        return $saved_file ? new BaseResource($saved_file) : throw new UnprocessableEntityHttpException();
    }


    /**
     * @OA\Get(
     *     path="/api/v1/files/{uuid}",
     *     operationId="read-file",
     *     summary="Read a file",
     *     tags={"File"},
     *     security={{}},
     *     @OA\Parameter(ref="#/components/parameters/uuid_path"),
     *     @OA\Response(response=200, ref="#/components/responses/OK"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/Unprocessable"),
     *     @OA\Response(response=500, ref="#/components/responses/ServerError")
     * )
     */
    public function show(string $uuid): BinaryFileResponse
    {
        /** @var File $file */
        $file = $this->file_repository->findByUuid($uuid);
        $file_path = storage_path($file->path);
        if (file_exists($file_path)) {
            return Response::download($file_path);
        }
        throw new ModelNotFoundException();
    }
}
