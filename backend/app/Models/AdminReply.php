<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminReply extends Model
{
    protected $fillable = [
        'document_request_id',
        'reply',
        'file_path',
        'file_name',
        'file_size'  // Add this if your table has this column
    ];

    public function request()
    {
        return $this->belongsTo(DocumentRequest::class, 'document_request_id');
    }
}
