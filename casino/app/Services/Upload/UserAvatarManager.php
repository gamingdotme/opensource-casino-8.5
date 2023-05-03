<?php

namespace VanguardLTE\Services\Upload;

use Illuminate\Http\UploadedFile;
use VanguardLTE\User;
use Illuminate\Filesystem\Filesystem;
use Intervention\Image\ImageManager;
use Symfony\Component\HttpFoundation\File\File;

class UserAvatarManager
{
    const AVATAR_WIDTH = 160;

    const AVATAR_HEIGHT = 160;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var Filesystem
     */
    private $fs;
    /**
     * @var ImageManager
     */
    private $imageManager;

    public function __construct(Filesystem $fs, ImageManager $imageManager)
    {
        $this->fs = $fs;
        $this->imageManager = $imageManager;
    }

    /**
     * Upload and crop user avatar to predefined width and height.
     *
     * @param User $user
     * @param UploadedFile $file
     * @param array|null $cropPoints
     * @return string Avatar file name.
     */
    public function uploadAndCropAvatar(User $user, UploadedFile $file, array $cropPoints = null)
    {
        list($name, $avatarImage) = $this->saveFile($file);

        try {
            $this->cropAndResizeImage($avatarImage, $cropPoints);
            $this->deleteAvatarIfUploaded($user);
        } catch (\Exception $e) {
            logger("Cannot upload avatar. " . $e->getMessage());
            $this->fs->delete($this->getDestinationDirectory() . "/" . $name);
            return null;
        }

        return $name;
    }

    /**
     * Check if user has uploaded avatar photo.
     * If he is using some external url for avatar, then
     * it is assumed that avatar is not uploaded manually.
     *
     * @param User $user
     * @return bool
     */
    private function userHasUploadedAvatar(User $user)
    {
        return $user->avatar && ! str_contains($user->avatar, ['http', 'gravatar']);
    }

    /**
     * Save avatar for provided user.
     *
     * @param UploadedFile $uploadedFile
     * @return array
     */
    private function saveFile(UploadedFile $uploadedFile)
    {
        $name = $this->generateAvatarName();

        $targetFile = $uploadedFile->move(
            $this->getDestinationDirectory(),
            $name
        );

        return [$name, $targetFile];
    }

    /**
     * Get destination directory where avatar should be uploaded.
     *
     * @return string
     */
    private function getDestinationDirectory()
    {
        return public_path('upload/users');
    }

    /**
     * @param User $user
     */
    public function deleteAvatarIfUploaded(User $user)
    {
        if (! $this->userHasUploadedAvatar($user)) {
            return;
        }

        $path = sprintf(
            "%s/%s",
            $this->getDestinationDirectory(),
            $user->avatar
        );

        $this->fs->delete($path);
    }

    /**
     * Build random avatar name.
     *
     * @return string
     */
    private function generateAvatarName()
    {
        return sprintf("%s.png", str_random());
    }

    /**
     * Crop image from provided selected points and
     * resize it to predefined width and height.
     *
     * @param File $avatarImage
     * @param array|null $points
     * @return \Intervention\Image\Image
     */
    private function cropAndResizeImage(File $avatarImage, array $points = null)
    {
        $image = $this->imageManager->make(
            $avatarImage->getRealPath()
        );

        if ($points) {
            // Calculate delta between two points on X axis. This
            // value will be used as width and height for cropped image.
            $size = floor($points['x2'] - $points['x1']);

            return $image->crop($size, $size, (int) $points['x1'], (int) $points['y1'])
                ->resize(self::AVATAR_WIDTH, self::AVATAR_HEIGHT)
                ->save();
        }

        // If crop points are not provided, we will just crop
        // provided image to specified width and height.
        return $image->crop(self::AVATAR_WIDTH, self::AVATAR_HEIGHT)
            ->save();
    }
}