<?php

function createSlug($filename, $maxLength = 100)
{
    static $counter = 0;

    $counter++;

    // Replace non-alphanumeric characters with hyphens
    $slug = $counter.'-'.preg_replace('/[^A-Za-z0-9-]+/', '-', pathinfo($filename, PATHINFO_FILENAME));

    // Truncate slug to the max length
    return substr($slug, 0, $maxLength);
}

function scanDirectory($directory, &$files = [])
{
    if (!is_dir($directory)) {
        throw new Exception("The provided directory does not exist.");
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveCallbackFilterIterator(
            new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS),
            function ($current) {

                static $lastDir = '';

                if ($current->isDir()) {
                    $currentDir = $current->getRealPath();
                    if ($current->isReadable() && $currentDir !== $lastDir) {
                        // Use \r to overwrite the current line and \033[K to clear the rest of the line
                        echo "\rScanning directory: " . $currentDir . "\033[K";
                        $lastDir = $currentDir;
                        return true;
                    } else {
                        echo "\rSkipping unreadable directory: " . $currentDir . "\033[K" . "\n";
                        return false;
                    }
                } else {
                    return $current->isReadable();
                }
            }
        )
    );

    // After scanning is complete, print a new line to prevent overwriting the last output
    echo "\n";

    /** @var SplFileObject $file */
    foreach ($iterator as $file) {
        if ($file->isFile() && in_array($file->getExtension(), ['mus', 'musx', 'MUS', 'MUSX'])) {
            $slug         = createSlug($file->getFilename());
            $files[$slug] = $file->getRealPath();
        }
    }

    return $files;
}

function copyFiles($files, $destinationDir)
{
    if (!is_dir($destinationDir)) {
        throw new Exception("Failed to create the destination directory.");
    }

    foreach ($files as $slug => $filePath) {
        $destinationPath = $destinationDir.DIRECTORY_SEPARATOR.$slug.'.'.pathinfo($filePath, PATHINFO_EXTENSION);
        if (!copy($filePath, $destinationPath)) {
            echo "Failed to copy $filePath to $destinationPath\n";
        } else {
            echo "Found file: $filePath\n";
        }
    }
}

try {
    $directory    = $argv[1] ?? null;
    $targetDir    = __DIR__.DIRECTORY_SEPARATOR.'scores';
    $jsonFilePath = __DIR__.DIRECTORY_SEPARATOR.'scores.json';

    // If the destination directory or JSON file exists, prompt the user to delete them
    if (is_dir($targetDir) || file_exists($jsonFilePath)) {
        echo "The target directory or 'scores.json' file already exists. This means that you have already run this script before.\n";
        echo "If you want to start from scratch, please answer y to the following question. Otherwise answer N and the script will quit.\n";
        echo "Do you want to delete them and continue? (y/N): ";
        $handle   = fopen("php://stdin", "r");
        $response = trim(fgets($handle));
        fclose($handle);

        if (strtolower($response) !== 'y') {
            echo "Operation aborted.\n";
            exit(1);
        }

        // Use bash to recursively delete the directory
        if (is_dir($targetDir)) {
            $command = 'rm -rf '.escapeshellarg($targetDir);
            shell_exec($command);
            echo "Target directory deleted.\n";
        }

        if (file_exists($jsonFilePath)) {
            if (!unlink($jsonFilePath)) {
                throw new Exception("Failed to delete the 'scores.json' file.");
            }
        }

        echo "Target directory and JSON file deleted.\n";
    }

    echo "Source directory: $directory\n";
    echo "Target directory: $targetDir\n";
    echo "JSON file path: $jsonFilePath\n";

    if (!is_readable($directory)) {
        throw new Exception("The provided source directory does not exist or is not readable.");
    }
    if (!mkdir($targetDir, 0766, true) || !is_writable($targetDir)) {
        throw new Exception("The target directory is not writable.");
    }

    if (!$directory) {
        throw new Exception("Please provide a directory as an argument.");
    }

    $files = scanDirectory($directory);

    copyFiles($files, $targetDir);

    if (file_put_contents($jsonFilePath, json_encode($files, JSON_PRETTY_PRINT)) === false) {
        throw new Exception("Failed to write the JSON file.");
    }

    echo count($files)." files processed successfully. Check the 'scores' directory and the 'scores.json' file.\n";
} catch (Exception $e) {
    echo "Error: ".$e->getMessage()."\n";
    exit(1);
}
