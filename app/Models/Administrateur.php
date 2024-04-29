<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Administrateur extends Model
{
    use HasFactory;

    /** @return BelongsTo<User, Administrateur> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
