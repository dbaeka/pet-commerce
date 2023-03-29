<?php

namespace Application;

use App\Models\File;
use App\Models\User;
use Database\Factories\FileFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File as FileFacade;
use Tests\Feature\Application\ApiTestCase;

class FileTest extends ApiTestCase
{
    use RefreshDatabase;

    private string $sample_image;

    public function testReadFile(): void
    {
        $endpoint = self::PREFIX . 'files/';

        /** @var File $file */
        $file = FileFactory::new()->local($this->sample_image)->create();
        $this->get($endpoint . $file->uuid)
            ->assertOk()
            ->assertDownload();
    }

    public function testUploadFile(): void
    {
        $endpoint = self::PREFIX . 'files/upload';

        /** @var User $user */
        $user = UserFactory::new()->admin()->create();

        $response = $this->postAs($endpoint, [
            'file' => UploadedFile::fake()->image('avatar.jpg')
        ], $user);

        $response->assertCreated()
            ->assertJsonStructure($this->mergeDefaultFields(
                "uuid",
                "name",
                "type",
                "path"
            ))
            ->assertJsonFragment([
                'success' => 1,
            ]);

        self::assertDatabaseHas('files', [
            'uuid' => $response->json('data.uuid'),
        ]);

        $path = storage_path($response->json('data.path'));
        self::assertFileExists($path);

        $this->post($endpoint, [
            'image' => UploadedFile::fake()->image('avatar.jpg')
        ])->assertUnprocessable()
            ->assertJsonStructure($this->mergeDefaultFields())
            ->assertJsonFragment([
                'success' => 0
            ]);
    }

    protected function setUp(): void
    {
        parent::setUp();
        config(['app.pet_shop_file_dir' => 'sandbox/pet-shop/']);

        @mkdir(storage_path(config('app.pet_shop_file_dir')), 0755, true);

        $this->sample_image = 'tests/Fixtures/sample_image.png';
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        FileFacade::deleteDirectory(storage_path('sandbox'));
    }
}
