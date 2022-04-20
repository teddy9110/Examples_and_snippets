<?php

namespace Rhf\Modules\Competition\Services;

use Illuminate\Support\Facades\Auth;
use Rhf\Modules\Competition\Models\CompetitionVotes;

class CompetitionVotesService
{
    /**
     * @param $entryId
     * @return mixed
     */
    public function hasVoted($entryId)
    {
        return CompetitionVotes::where('entry_id', $entryId)
            ->where('user_id', Auth::id())
            ->exists();
    }

    /**
     * @param $entryId
     * @return mixed
     */
    public function getVote($entryId)
    {
        return CompetitionVotes::where('entry_id', $entryId)
            ->where('user_id', Auth::id())
            ->first();
    }

    /**
     * @param $entryId
     * @return mixed
     */
    public function castVote($entryId)
    {
        return CompetitionVotes::create([
            'entry_id' => $entryId,
            'user_id' => Auth::id()
        ]);
    }

    /**
     * @param $entryId
     */
    public function deleteVote($entryId)
    {
        $voted = $this->getVote($entryId);
        return $voted->delete();
    }
}
