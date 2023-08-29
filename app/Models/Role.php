<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function users()  {
        return $this->hasMany(User::class);
    }

    public const superAdmin = 1;
    public const admin = 2;
    public const user = 3;
}
