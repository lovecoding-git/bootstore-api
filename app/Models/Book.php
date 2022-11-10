<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'description', 'image', 'price', 'user_id', 'published'];

    public function author()
    {
        return $this->belongsTo(User::class, "user_id");
    }
    //function to check if a book is published
    public function isPublished()
    {
        return $this->published === 1;
    }
    //custom query scope to filter published books only
    public function scopePublished($query)
    {
        return $query->where('published', 1);
    }
    //custom query scope to filter only authenticated user's books
    public function scopeMy($query)
    {
        return $query->where('user_id', auth()->user()->id);
    }
}
