<?php

namespace App\Models;

use Cog\Contracts\Ownership\CanBeOwner;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Model\HasMembers;
use App\Interfaces\Controller\v1\CanHaveEvents;
use App\Interfaces\Controller\v1\CanHaveCalendars;
use App\Interfaces\Controller\v1\CanHaveContacts;

class Group extends Model implements CanBeOwner, CanHaveCalendars, CanHaveEvents, CanHaveContacts
{
    use SoftDeletes, HasMembers;

    public static function boot() {
        static::created(function ($model) {
            $model->assignRoles('group admin', [
				'user_id' => $model->user_id,
				'validated_by' => $model->user_id,
				'semester_id' => 0,
			], true);
        });
    }

	protected $roleRelationTable = 'groups_members';

    protected $fillable = [
        'name', 'user_id', 'icon_id', 'visibility_id', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $dates = [
        'deleted_at',
    ];

    protected $hidden = [
        'user_id', 'visibility_id',
    ]; // On les caches car on récupère directement le user et la vibility dans le controller

    public function hideData(array $params = []): Model {
        return $this->makeHidden(['icon', 'created_at', 'updated_at', 'deleted_at']);
    }

    public function owner() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function visibility() {
    	return $this->belongsTo(Visibility::class, 'visibility_id');
    }

	// Par défaut, un role n'est pas supprimable s'il a déjà été assigné
    // Mais on permet sa suppression s'il est assigné à un seul groupe
	public function isRoleForIdDeletable($role, $id) {
		return true;
	}

    public function contacts() {
    	return $this->morphMany(Contact::class, 'owned_by');
    }

	public function isContactAccessibleBy(int $user_id): bool {
		return $this->currentMembers()->wherePivot('user_id', $user_id)->exists();
	}

	public function isContactManageableBy(int $user_id): bool {
		return $this->hasOnePermission('group contact', [
			'user_id' => $user_id,
		]);
	}

    public function calendars() {
    	return $this->morphMany(Calendar::class, 'owned_by');
    }

	public function isCalendarAccessibleBy(int $user_id): bool {
		return $this->currentMembers()->wherePivot('user_id', $user_id)->exists();
	}

	public function isCalendarManageableBy(int $user_id): bool {
		return $this->hasOnePermission('group calendar', [
			'user_id' => $user_id,
		]);
	}

    public function events() {
    	return $this->morphMany(Event::class, 'owned_by');
    }

	public function isEventAccessibleBy(int $user_id): bool {
		return $this->currentMembers()->wherePivot('user_id', $user_id)->exists();
	}

	public function isEventManageableBy(int $user_id): bool {
		return $this->hasOnePermission('group event', [
			'user_id' => $user_id,
		]);
	}
}
