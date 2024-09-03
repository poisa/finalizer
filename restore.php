<?php

function restoreMxlFiles($jsonFilePath, $force = false, $interactive = false)
{
    if (!file_exists($jsonFilePath)) {
        throw new Exception("The JSON file does not exist.");
    }

    $files = json_decode(file_get_contents($jsonFilePath), true);
    if (!$files) {
        throw new Exception("Failed to parse the JSON file.");
    }

    foreach ($files as $slug => $originalPath) {
        $originalFilename = pathinfo($originalPath, PATHINFO_FILENAME);
        $mxlFilePath      = __DIR__.DIRECTORY_SEPARATOR.'scores'.DIRECTORY_SEPARATOR.$slug.'.mxl';
        $destinationDir   = dirname($originalPath);
        $destinationPath  = $destinationDir.DIRECTORY_SEPARATOR.$originalFilename.'.mxl';

        if (!file_exists($mxlFilePath)) {
            echo "MXL file for $slug not found.\n";
            continue;
        }

        if (file_exists($destinationPath)) {
            if ($force) {
                moveFile($mxlFilePath, $destinationPath);
            } elseif ($interactive) {
                promptAndMoveFile($mxlFilePath, $destinationPath);
            } else {
                echo "Skipping file $destinationPath exists.\n";
            }
        } else {
            moveFile($mxlFilePath, $destinationPath);
        }
    }
}

function moveFile($source, $destination)
{
    if (!rename($source, $destination)) {
        echo "Failed to move $source to $destination\n";
    } else {
        echo "Moved $source to $destination\n";
    }
}

function promptAndMoveFile($source, $destination)
{
    echo "File $destination already exists. Overwrite? (y/N): ";
    $handle   = fopen("php://stdin", "r");
    $response = trim(fgets($handle));
    fclose($handle);

    if (strtolower($response) === 'y') {
        moveFile($source, $destination);
    } else {
        echo "Skipping $source\n";
    }
}

try {
    $jsonFilePath = __DIR__.DIRECTORY_SEPARATOR.'scores.json';

    $force       = in_array('--force', $argv);
    $interactive = in_array('--interactive', $argv);

    restoreMxlFiles($jsonFilePath, $force, $interactive);

    echo "MXL files restored successfully.\n";
} catch (Exception $e) {
    echo "Error: ".$e->getMessage()."\n";
    exit(1);
}