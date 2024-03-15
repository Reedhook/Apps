<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = false;
    protected $table = 'projects';

    /**
     * Определение отношения "многие ко многим" между Project и Platform
     * @return BelongsToMany
     */
    public function platforms(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        // внешний ключ для текущей модели project_id
        // внешний ключ для связанной таблицы platform_id
        return $this->belongsToMany(Platform::class, 'projects_platforms', 'project_id', 'platform_id');
    }

    /**
     * Определение отношения "многие ко многим" между Projects и Users
     * @return BelongsToMany
     */
    public function users(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        // внешний ключ для текущей модели project_id
        // внешний ключ для связанной таблицы platform_id
        return $this->belongsToMany(User::class, 'projects_users', 'project_id', 'user_id');
    }

    /**
     * Определение отношения "один ко многим" между Project и Release
     * @return HasMany
     */
    public function releases(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Release::class, 'release_id', 'id');
    }
}
