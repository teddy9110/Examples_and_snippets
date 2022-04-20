<?php

namespace Rhf\Modules\System\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

interface FileServiceInterface
{
    /**
     * Given an uploaded file, will store the file on S3, returning
     * the stored details.
     *
     * @param UploadedFile $file
     * @param $path
     * @param bool $public
     * @return array
     */
    public function createFromUpload(UploadedFile $file, $path, $public = true);

    /**
     * Given a string, will store the file on S3, returning
     * the stored details.
     *
     * @param string $file
     * @param string $name
     * @param $path
     * @param bool $public
     * @return array
     */
    public function createFromString(string $file, string $name, $path, $public = true);

    /**
     * Given a model, deletes the file on S3 (not the model itself).
     *
     * @param $model Model
     * @return bool
     */
    public function delete($model);

    /**
     * Given a model, generates a download response from s3.
     *
     * @param $model Model
     * @return mixed
     */
    public function getProxyDownload($model);

    /**
     * Given a model, returns the public (or privately signed) url.
     *
     * @param $model Model
     * @return mixed
     */
    public function getPublicUrl($model);

    /**
     * Given a model, provides the proxy URL
     *
     * @param $model Model
     * @return string
     */
    public function getProxyUrl($model);
}
