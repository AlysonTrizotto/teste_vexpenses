<?php

namespace App\Models\v1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    protected $table = 'logs';

    protected $fillable = [
        'action',
        'description',
        'user_id',
    ];

    /**
     * Get the casts array.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
        ];
    }

    /**
     * Create a new log record.
     *
     * @param array $data
     * @return self
     */
    public static function store(array $data): self
    {
        return self::create($data);
    }

    /**
     * Retrieve a log record by its ID.
     *
     * @param int $id
     * @return self
     */
    public static function getById(int $id): self
    {
        return self::with('user')->findOrFail($id);
    }

    /**
     * Retrieve the list of logs with optional filters.
     *
     * @param array $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public static function index(array $filters = [])
    {
        return self::with('user')->when($filters['action'] ?? null, fn ($query, $action) =>
                    $query->where('action', 'like', "%{$action}%")
                )
                ->when($filters['user_id'] ?? null, fn ($query, $userId) =>
                    $query->where('user_id', $userId)
                )
                ->paginate(10);
    }

    /**
     * Get the user that owns the log.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
