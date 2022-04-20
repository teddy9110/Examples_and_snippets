<?php

namespace Rhf\Modules\Competition\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Redis;
use Rhf\Modules\Competition\Filters\CompetitionEntryFilter;
use Rhf\Modules\Competition\Models\Competition;
use Rhf\Modules\Competition\Models\CompetitionEntry;
use Rhf\Modules\Competition\Models\CompetitionVotes;

class EntryService
{
    /**
     * @var CompetitionEntry
     */
    private $competitionEntry;

    public function __construct(CompetitionImageService $competitionImageService)
    {
        $this->competitionImages = $competitionImageService;
    }

    /**
     * Check if a user already has a competition entry
     * checks soft deletes
     * @param $competitionId
     * @param $userId
     * @return bool
     */
    public function entryExists($competitionId, $userId): bool
    {
        return CompetitionEntry::withTrashed()
            ->where('competition_id', $competitionId)
            ->where('user_id', $userId)
            ->exists();
    }

    /**
     * @param $id
     * @param array $data
     * @param UploadedFile $image
     * @param Competition $competition
     * @return mixed
     */
    public function createEntry($id, array $data, UploadedFile $image, Competition $competition)
    {
        $entry = CompetitionEntry::create($data);
        $entryImage = $this->competitionImages->storeImage($image, $id, true);
        $url = config('app.competition_url') . '/entries/' . $entry->id;
        $entry->update([
            'image' => $entryImage['path'] . '/' . $entryImage['file_name'],
            'url' => $url
        ]);
        $this->competitionImages->restoreImage($entry);
        return $entry;
    }

    public function editEntry($id, array $data)
    {
        $entry = $this->getEntry($id, ['competition']);
        $entry->update([
            'description' => $data['description']
        ]);
        return $entry;
    }

    /**
     * get single entry
     *
     * @param $id
     * @param $relationship
     * @return mixed
     */
    public function getEntry($id, $relationships = [])
    {
        return CompetitionEntry::with($relationships)->findOrFail($id);
    }

    /**
     * get all entries for competition
     *
     * @param $id
     * @param CompetitionEntryFilter $filters
     * @param $pagination
     * @return mixed
     */
    public function getEntries($id, CompetitionEntryFilter $filters, $pagination)
    {
        return CompetitionEntry::where('competition_id', $id)
            ->where('suspended', 0)
            ->filter($filters)
            ->paginate($pagination['per_page'], '*', 'page', $pagination['page']);
    }

    public function getAdminEntries($id, CompetitionEntryFilter $filters, $pagination)
    {
        return CompetitionEntry::where('competition_id', $id)
            ->filter($filters)
            ->paginate($pagination['per_page'], '*', 'page', $pagination['page']);
    }

    /**
     * Get all user entries
     *
     * @param $uid
     * @return mixed
     */
    public function getUserEntries($uid)
    {
        return CompetitionEntry::where('user_id', $uid)
            ->with('competition')
            ->withTrashed()
            ->get();
    }

    public function getUserCompetitionEntries($id, $uid)
    {
        return CompetitionEntry::where('user_id', $uid)
            ->where('competition_id', $id)
            ->with('competition')
            ->withTrashed()
            ->get();
    }

    /**
     * Add 1 to votes
     *
     * @param $id
     * @return mixed
     */
    public function incrementVote($id)
    {
        $entry = $this->getEntry($id, ['competition']);
        $entry->increment('votes');
        return $entry;
    }

    /**
     * Subtract 1 from vote
     *
     * @param $id
     * @return mixed
     */
    public function decrementVote($id)
    {
        $entry = $this->getEntry($id, ['competition']);
        $entry->decrement('votes');
        return $entry;
    }

    /**
     * @param $id
     */
    public function deleteEntry($id, bool $report = false)
    {
        $entry = CompetitionEntry::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();
        if ($entry) {
            if ($report) {
                return $entry->delete();
            }

            $this->competitionImages->deleteImage($entry);
            return $entry->forceDelete();
        }
        return false;
    }

    /**
     * Checks if a user has voted
     *
     * @param $entryId
     * @return mixed
     */
    public function hasUserVoted($entryId)
    {
        return CompetitionVotes::where('entry_id', $entryId)
            ->where('user_id', Auth::id())
            ->exists();
    }

    /**
     * Returns a count of how many times a user has voted
     *
     * @param $entryId
     * @return mixed
     */
    public function canVote($entryId, $ip)
    {
        $key = 'web_vote:' . $entryId . ':' . $ip;
        return !is_null(Redis::get($key)) ? json_decode(Redis::get($key), true)['count'] : 0;
    }

    /**
     * Adds redis key
     *
     * @param $entryId
     * @param $value
     * @return mixed
     */
    public function castVote($entryId, $value)
    {
        $key = 'web_vote:' . $entryId . ':' . $value['ip'];
        $val = json_encode($value, JSON_FORCE_OBJECT);
        return Redis::set($key, $val);
    }

    /**
     * Suspend an entry
     * @param $id
     */
    public function suspendEntry($id): void
    {
        $entry = $this->getEntry($id, ['competition']);
        $entry->suspended = true;
        $entry->save();
        $this->competitionImages->suspendImage($entry);
    }

    /**
     * Unsuspend and entry
     * @param $id
     */
    public function unsuspendEntry($id): void
    {
        $entry = $this->getEntry($id, ['competition']);
        $entry->suspended = false;
        $entry->save();
        $this->competitionImages->restoreImage($entry);
    }

    /**
     * Restore a soft deleted entry
     * @param $id
     */
    public function restoreEntry($id): void
    {
        $entry = $this->getEntry($id, ['competition']);
        $entry->restore();
    }
}
