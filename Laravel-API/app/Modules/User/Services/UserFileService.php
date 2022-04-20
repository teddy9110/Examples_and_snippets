<?php

namespace Rhf\Modules\User\Services;

use Rhf\Modules\System\Contracts\FileServiceInterface;
use Rhf\Modules\System\Services\FileService;

class UserFileService extends FileService implements FileServiceInterface
{
    public function generatePath($id)
    {
        return 'users/' . $id . '/images/';
    }
}
