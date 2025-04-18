<?php

namespace App\Models\v1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FileManagement extends Model
{
    use HasFactory, SoftDeletes;


    protected $table = 'files_managements';

    protected $fillable = [
        'name',
        'original_name',
        'path',
        'status',
        'error',
    ];

    
    /**
     * Get the casts array.
     * 
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => 'integer',
        ];
    }

   
    /**
     * Create a new file management record.
     * 
     * @param array $data The data to create the record with.
     * @return self The created record.
     */
    public static function store(array $data): self
    {
        return self::create($data);
    }

    
    /**
     * Update a file management record.
     * 
     * @param int $id The ID of the record to update.
     * @param array $data The data to update the record with.
     * @return self The updated record.
     */
    public static function updateFile(int $id, array $data): self
    {
        $file = self::findOrFail($id);
        $file->update($data);
        return $file;
    }

   
    /**
     * Retrieve a file management record by its ID.
     * 
     * @param int $id
     * @return self|null
     */

    public static function getById(int $id): ?self
    {
        return self::findOrFail($id);
    }

    
    /**
     * Retrieve the list of files management records
     * 
     * @param array $filters
     * 
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public static function index(array $filters = [])
    {
        return self::when($filters['status'] ?? null, fn ($query, $status) =>
                    $query->where('status', $status)
                )
                ->when($filters['name'] ?? null, fn ($query, $name) =>
                    $query->where('name', 'like', "%{$name}%")
                )
                ->paginate(10);
    }
}
