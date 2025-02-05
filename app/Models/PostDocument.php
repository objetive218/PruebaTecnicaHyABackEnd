<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostDocument extends Model
{
    /** @use HasFactory<\Database\Factories\PostDocumentFactory> */
    use HasFactory;
    protected $fillable = [
        'title',
        'body',
    ];
    public function user(){
        return $this->belongsTo(User::class);
    }
}
