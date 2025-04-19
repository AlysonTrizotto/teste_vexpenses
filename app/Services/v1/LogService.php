<?php

namespace App\Services\v1;

use App\Models\v1\Log;

class LogService
{
    /**
     * Retrieve a paginated list of logs with optional filters.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(array $data)
    {
        return Log::index($data);
    }

    /**
     * Store a new log record.
     *
     * @param array $data
     * @return Log
     */
    public function store(array $data): Log
    {
        return Log::store($data);
    }

    /**
     * Retrieve a specific log record by ID.
     *
     * @param int $id
     * @return Log
     */
    public function getById(int $id): Log
    {
        return Log::getById($id);
    }
}
