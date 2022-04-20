<?php

namespace Rhf\Modules\Facebook\Services;

use Exception;
use Illuminate\Http\UploadedFile;
use Rhf\Modules\Admin\Services\AdminNotificationService;
use Rhf\Modules\Facebook\Models\FacebookVideo;
use Rhf\Modules\Notifications\Models\Topics;

class FacebookVideoService
{
    /** @var FacebookVideo */
    protected $facebookVideo = null;
    private $adminNotificationService;

    public function __construct(AdminNotificationService $adminNotificationService)
    {
        $this->adminNotificationService = $adminNotificationService;
    }

    /**
     * Creates a facebook video thumbnail
     *
     * @param UploadedFile $image
     * @return void
     * @throws Exception
     */
    public function createFacebookVideoThumbnail(UploadedFile $image)
    {
        $fileService = new FacebookVideoThumbnailFileService();
        $imagePath = $fileService->createFromUpload($image, 'facebook-video-thumbnails', false);
        $this->facebookVideo->thumbnail = $imagePath['path'] . '/' . $imagePath['file_name'];
    }

    /**
     * Deletes a facebook video thumbnail
     *
     * @return void
     * @throws Exception
     */
    public function deleteFacebookVideoThumbnail()
    {
        $fileService = new FacebookVideoThumbnailFileService();
        $fileService->delete($this->thumbnail);
    }

    /**
     * Creates a facebook video
     *
     * @param array $data
     * @param UploadedFile $image
     * @return FacebookVideo
     * @throws Exception
     */
    public function createFacebookVideo(array $data, UploadedFile $image)
    {
        $facebookVideo = new FacebookVideo();

        $this->setFacebookVideo($facebookVideo);
        $this->updateFacebookVideo($data, $image);

        return $this->facebookVideo;
    }

    /**
     * Update a facebook video
     *
     * @param UploadedFile $image
     * @throws Exception
     */
    public function updateFacebookVideoThumbnail(UploadedFile $image)
    {
        $this->createFacebookVideoThumbnail($image);

        // remove existing image
        if (isset($this->facebookVideo->thumbnail)) {
            $count = FacebookVideo::where('thumbnail', $image)->count();

            // only delete image if not referenced elsewhere
            if ($count == 1) {
                $this->deleteFacebookVideoThumbnail();
            }
        }

        $this->getFacebookVideo()->save();
    }

    /**
     * Update a facebook video
     *
     * @param array $data
     * @param UploadedFile $image
     * @throws Exception
     */
    public function updateFacebookVideo(array $data, UploadedFile $image = null)
    {
        $facebookVideo = $this->getFacebookVideo();

        foreach ($facebookVideo->getPlainKeys() as $key) {
            if (isset($data[$key])) {
                if (isset($data['live']) && $data['live'] === 'true') {
                    $data['live'] = true;
                }
                $facebookVideo[$key] = $data[$key];
            }
        }

        if (isset($data['live']) && $data['live'] === true) {
            $notification = $this->adminNotificationService->createNotification(
                [
                    'title' => "We're Live now!",
                    'content' => 'Click to watch',
                    'topic_id' => Topics::where('slug', 'like', '%live_%')->firstOrFail()->id,
                    'data' => ['deep_link_location' => 'news']
                ]
            );
            $this->adminNotificationService->setNotification($notification);
            $this->adminNotificationService->sendNotification();
        }

        if (isset($image)) {
            $this->updateFacebookVideoThumbnail($image);
        }

        $facebookVideo->save();
    }

    /**
     * Return the item associated to the instance of the service.
     *
     * @return FacebookVideo
     */
    public function getFacebookVideo()
    {
        return $this->facebookVideo;
    }

    /**
     * Set the facebook video associated to the instance of the service.
     *
     * @param FacebookVideo $facebookVideo
     * @return self
     */
    public function setFacebookVideo(FacebookVideo $facebookVideo)
    {
        $this->facebookVideo = $facebookVideo;
        return $this;
    }
}
