<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class PostDocument extends Model
{
    /** @use HasFactory<\Database\Factories\PostDocumentFactory> */
    use HasFactory;
    protected $fillable = [
        'title',
        'body',
        'file_path'
    ];
    
    public function user(){
        return $this->belongsTo(User::class);
    }
}
