<?php

namespace App\Traits;

use App\Helpers\LibConstants;
use Carbon\Carbon;
use Gumlet\ImageResize;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

/**
 * Traits HelperTrait
 * @package App\Traits
 */
trait HelperTrait
{
    /**
     * @param $image
     * @param $isMultiImg
     * @return array|string
     */
    public function getImages($image, $isMultiImg = false): array|string
    {
        $images = [];
        $imageFiles= explode(',', $image);
        if ($isMultiImg) {
            foreach ($imageFiles as $img) {
                $images[] = [
                    'name' => $img
                ];
            }
            return $images;
        }
        return $imageFiles[0];
    }

    /**
     * @param $imageFiles
     * @return string
     * @throws \Gumlet\ImageResizeException
     */




public function uploadImage($imageFile, $path): string
{
    $dir = isset($path) ? $path : LibConstants::IMAGE_PATH;

    // Ensure the directory exists
    if (!File::exists(public_path($dir))) {
        File::makeDirectory(public_path($dir), 0777, true, true);
    }

    // Check if the uploaded file is valid
    if ($imageFile instanceof UploadedFile && $imageFile->isValid()) {
        // Generate a unique name for the file
        $name = Str::random(40) . '.' . $imageFile->getClientOriginalExtension();

        // Move the file to the target directory
        $imageFile->move(public_path($dir), $name);

        // Return the name of the file
        return $name;
    }

    return ''; // Return an empty string if the file wasn't provided or isn't valid
}





    /**
     * @param $images
     * @return void
     */
    public function updateImages($images): void
    {
        foreach (explode(',', $images) as $img) {
            $pathIMG = public_path() . LibConstants::IMAGE_PATH . $img;
            if (file_exists($pathIMG)) {
                @unlink($pathIMG);
            }
        }
    }

    /**
     * @return array
     */
    private static function formatDateString(): array
    {
        $today = Carbon::now();
        return [
            'now'        => $today->toDateTimeString(),
            'month_date' => $today->format('md'),
            'year'       => $today->format('Y')
        ];
    }


    
    /**
     * @param $pageName
     * @return string
     */
    public function generateUrlSlug($pageName): string
    {
        $pageName = preg_replace('~[^\pL\d]+~u', '-', $pageName);
        $pageName = preg_replace('~[^-\w]+~', '', $pageName);
        $pageName = trim($pageName, '-');
        $pageName = preg_replace('~-+~', '-', $pageName);
        $pageName = strtolower($pageName);

        if (empty($pageName)) {
            return 'n-a';
        }

        return $pageName;
    }

   
}
