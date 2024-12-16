<?php

namespace App\Service;

class UniqueFilenameGenerator
{
    public function generateUniqueFilename(string $imageName, string $imageExtension)
    {
        $currentTimestamp = time();
        $nameHashed = hash('sha256', $imageName);

        $imageNewName = uniqid() . '-' . $nameHashed . '-' . $currentTimestamp . '.' . $imageExtension;

        return $imageNewName;
    }

}