<?php

namespace App\Models\v1;

use App\Notifications\v1\CustomResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens,  HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'birth_date',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'date',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the identifier that will be stored in the JWT subject claim.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Get custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            "user" => [
                'id' => $this->id,
                'name' => $this->name,
                'email' => $this->email,
                'birth_date' => $this->birth_date,
            ],
        ];
    }

    
    protected static function store(array $data){
        return self::create($data);
    }

    protected static function me(int $userId)
    {
        return self::select('id', 'name', 'email', 'birth_date')
                    ->where('id', $userId)
                    ->first();
    
    }

    public static function index(array $data){
        return self::select('id', 'name', 'email', 'birth_date')
                    ->when($data['name'] ?? null, function ($query, $name) {
                        $query->where('name', 'like', "%{$name}%");
                    })
                    ->when($data['email'] ?? null, function ($query, $email) {
                        $query->where('email', 'like', "%{$email}%");
                    })
                    ->paginate(10);
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPassword($token));
    }
}
