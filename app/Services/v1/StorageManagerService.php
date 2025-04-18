<?php

namespace App\Services\v1;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;

class StorageManagerService
{
    /**
     * StorageManager constructor.
     *
     * Initializes the storage using the specified Filesystem interface.
     *
     * @param Filesystem $storage
     */
    public function __construct(
        private Filesystem $storage
    ){
        $this->storage = Storage::disk('local');
    }

    /**
     * Get a file from the storage.
     *
     * @param string $filePath
     *
     * @return string|array
     */
    public function getFile(string $filePath)
    {
        $fileExists = $this->fileExists($filePath);
        return is_array($fileExists) ? $fileExists : $this->storage->get($filePath) ;
    }

    /**
     * Stores a file at the specified path in the storage.
     *
     * @param string $filePath The path where the file should be stored.
     * @param mixed $file The file content to be stored.
     *
     * @return bool Returns true on success, false on failure.
     */

    public function putFile(string $filePath, $file, string $name): bool
    {
        return $this->storage->putFileAs($filePath, $file, $name);;
    }

    /**
     * Checks if a file exists at the given path.
     *
     * @param string $filePath The path to the file.
     * @return bool|array Returns true if the file exists, or an array with a success flag and message if not.
     */
    public function fileExists(string $filePath): bool|array
    {
        return !$this->storage->exists($filePath) ?
            ['success' => 0, 'message' => 'File not found!']
            : true;
    }

    /**
     * Deletes a file from the given path.
     *
     * @param string $filePth
     *
     * @return bool
     */
    public function deleteFile(string $filePth): bool
    {
        return  $this->fileExists($filePth) ? $this->storage->delete($filePth) : false;
    }

    public function getPath(string $filePath): string|array
    {
        $fileExists = $this->fileExists($filePath);
        return is_bool($fileExists) ? (string)$this->storage->path($filePath) : $fileExists;
    }
}
