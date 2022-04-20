<?php

namespace Rhf\Modules\Competition\Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Rhf\Modules\Competition\Models\Competition;
use Rhf\Modules\Competition\Services\CompetitionImageService;
use Rhf\Modules\Competition\Services\CompetitionService;
use Tests\Feature\ApiTest;

class CompetitionTest extends ApiTest
{
    use DatabaseTransactions;

    protected $date;
    public $competitionService;

    public function setUp(): void
    {
        parent::setUp();
        $this->setUpUser();
        $this->date = date('Y-m-d');
        $this->competitionImageService = new CompetitionImageService();
        $this->competitionService = new CompetitionService($this->competitionImageService);
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function testCompetitionCreation()
    {
        $competition = Competition::factory()->make();
        $desktopImage = UploadedFile::fake()->create(Str::random(16) . '.jpg');
        $mobileImage = UploadedFile::fake()->create(Str::random(16) . '.jpg');
        $appImage = UploadedFile::fake()->create(Str::random(16) . '.jpg');
        $createCompetition = $this->competitionService->createCompetition(
            $competition->toArray(),
            $desktopImage,
            $mobileImage,
            $appImage
        );
        $this->assertDatabaseHas('competitions', [
            'title' => $competition->title,
            'subtitle' => $competition->subtitle
        ]);
    }

    public function testCompetitionUpdate()
    {
        $competition = Competition::factory()->create();
//        $desktopImage = UploadedFile::fake()->create(Str::random(16) . '.jpg');
//        $mobileImage = UploadedFile::fake()->create(Str::random(16) . '.jpg');
//        $appImage = UploadedFile::fake()->create(Str::random(16) . '.jpg');

        $competition->title = ' - updated';
        $updateCompetition = $this->competitionService->updateCompetition($competition->id, $competition);

        $this->assertDatabaseHas('competitions', [
            'title' => ' - updated',
        ]);
    }

    public function testImageUpdate()
    {
        $competition = Competition::factory()->create();
        $image = UploadedFile::fake()->create('Updated Image.jpg');
        $updateImage = $this->competitionService->updateImage(
            $competition->id,
            $image,
            'desktop'
        );

        $this->assertDatabaseHas('competitions', [
            'desktop_image' => $updateImage->desktop_image
        ]);

        $this->assertNotEquals($updateImage->desktop_iamge, $competition->desktop_image);
    }

    public function testCompetitionDeletion()
    {
        $competition = Competition::factory()->create();
        $delete = $this->competitionService->deleteCompetition($competition->id);
        $this->assertDatabaseMissing('competitions', $competition->toArray());
    }

    public function testCompetitionRestore()
    {
        $competition = Competition::factory()->create();
        $delete = $this->competitionService->deleteCompetition($competition->id);
        $this->assertDatabaseMissing('competitions', $competition->toArray());
        $restore = $this->competitionService->restoreCompetition($competition->id);
        $competition->active = false;
        $this->assertDatabaseHas('competitions', $competition->toArray());
    }
}
