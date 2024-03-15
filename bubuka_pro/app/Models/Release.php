<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Release extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = false;
    protected $table = 'releases';

    /**
     * Определение отношения "один ко многим" между Project и Release
     * @return BelongsTo
     */
    public function project(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    /**
     * Определение отношения "один ко многим" между Release и Release_type
     * @return BelongsTo
     */
    public function release_type(): BelongsTo
    {
        return $this->belongsTo(Release_type::class, 'release_type_id', 'id');
    }

    /**
     * Определение отношения "один ко многим" между Release и TechnicalRequirement
     * @return BelongsTo
     */
    public function technical_requirement(): BelongsTo
    {
        return $this->belongsTo(Release_type::class, 'technical_requirement_id', 'id');
    }

    /**
     * Определения отношения "один ко многим" между Release и Changes
     * @return HasMany
     */
    public function changes(): hasMany
    {
        return $this->hasMany(ChangeLog::class, 'release_id','id');
    }
}
