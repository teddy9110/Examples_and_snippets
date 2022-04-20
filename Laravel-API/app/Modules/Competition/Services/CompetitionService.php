<?php

namespace Rhf\Modules\Competition\Services;

use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Rhf\Modules\Competition\Filters\CompetitionFilter;
use Rhf\Modules\Competition\Models\Competition;

class CompetitionService
{
    /**
     * @var Competition
     */
    private $competition;
    /**
     * @var CompetitionImageService
     */
    private $competitionImages;

    public function __construct(CompetitionImageService $competitionImageService)
    {
        $this->competitionImages = $competitionImageService;
    }

    public function getAll()
    {
        return Competition::where('active', 1)->get();
    }

    public function getAllWebsite()
    {
        return Competition::whereClosed(0)
            ->whereActive(1)
            ->get();
    }

    /**
     * Return all competitions
     *
     * @return mixed
     */
    public function paginate(CompetitionFilter $filters, array $pagination)
    {
        return Competition::filter($filters)
            ->paginate($pagination['per_page'], '*', 'page', $pagination['page']);
    }

    /**
     * Return latest competition
     *
     * @return mixed
     */
    public function getLatest()
    {
        return Competition::where('active', 1)
            ->where('closed', 0)
            ->latest('start_date')
            ->take(1)
            ->get();
    }

    public function getBySlug($slug)
    {
        return Competition::where('slug', $slug)
            ->with(['entries' => function ($query) {
                $query->latest()->limit(12);
            }])
            ->with(['winner', 'leaderboard' => function ($q) {
                $q->limit(3)->take(3);
            }])
            ->first();
    }

    /**
     * Return previous competitions where date
     * is less than or equal to now
     *
     * @param $pagination
     * @return mixed
     */
    public function getPrevious($pagination)
    {
        return Competition::whereClosed(1)
            ->with('winner')
            ->orderBy('start_date', 'desc')
            ->paginate($pagination['per_page'], '*', 'page', $pagination['page']);
    }

    public function getAllPrevious()
    {
        return Competition::whereClosed(1)
            ->with('winner')
            ->get();
    }

    /**
     * Get Competition by ID
     *
     * @param $id
     */
    public function getCompetition($id)
    {
        return Competition::with(['entries','winner', 'leaderboard'])->findOrFail($id);
    }

    /**
     * Create competition
     *
     * @param array $data
     * @param UploadedFile $image
     * @return mixed
     */
    public function createCompetition(
        array $data,
        UploadedFile $desktopImage,
        UploadedFile $mobileImage,
        UploadedFile $appImage
    ) {

        if (isset($data['active'])) {
            $data['active'] = $data['active'] == 'true' ? 1 : 0;
        }

        $data['rules'] = json_encode($data['rules']);
        $data['description'] = json_encode($data['description']);

        unset($data['desktop_image']);
        unset($data['mobile_image']);
        unset($data['app_image']);

        $competition = Competition::create($data);
        $this->updateCompetitionImage($desktopImage, $competition, 'desktop');
        $this->updateCompetitionImage($mobileImage, $competition, 'mobile');
        $this->updateCompetitionImage($appImage, $competition, 'app');

        return $competition;
    }

    public function updateCompetition($id, $data)
    {
        if (isset($data['active'])) {
            $data['active'] = $data['active'] == 'true' ? 1 : 0;
        }
        $data['rules'] = json_encode($data['rules']);
        $data['description'] = json_encode($data['description']);
        $comp = $this->getCompetition($id);

        $competition = new Competition();
        foreach ($competition->getPlainKeys() as $key) {
            $comp->update([
                $key => $data[$key]
            ]);
        }
        return $comp;
    }

    public function updateImage($id, $image, $type)
    {
        if (!is_null($image)) {
            $competition = $this->getCompetition($id);
            $this->competitionImages->deleteImage($competition);
            $this->updateCompetitionImage($image, $competition, $type);
            return $competition;
        }
        return false;
    }

    public function deleteCompetition($id)
    {
        $comp = $this->getCompetition($id);
        $comp->active = false;
        $comp->save();
        return $comp->delete();
    }

    public function restoreCompetition($id)
    {
        $comp = Competition::withTrashed()->findOrFail($id);
        return $comp->restore();
    }

    /**
     * @param $image
     * @param $competition
     * @throws \Exception
     */
    private function updateCompetitionImage(UploadedFile $image, $competition, $type): void
    {
        $competitionImage = $this->competitionImages->storeImage($image, $competition->id, false);
        $competition->update(
            [
                $type . '_image' => $competitionImage['path'] . '/' . $competitionImage['file_name']
            ]
        );
    }
}
