<!-- app/Models/User.php -->

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = ['name','email','password'];
    protected $hidden = ['password','remember_token'];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
