<?php

namespace App\Models;

use \DateTimeInterface;
use Carbon\Carbon;
use Hash;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class User extends Authenticatable
{
    use SoftDeletes;
    use Notifiable;
    use HasFactory;

    /**
     * Columns defined here should not be shown in any retrieved instance of this model
     *
     * @var string[]
     */
    protected $hidden = [
        'remember_token',
        'password',
    ];

    /**
     * @var string[]
     */
    protected $dates = [
        'email_verified_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'is_active',
        'password',
        'remember_token',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Cast variables to specified data types
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /************************************
     * C U S T O M  P R O P E R T I E S *
     ***********************************/

    public function getIsAdminAttribute()
    {
        return $this->roles()->where('id', 1)->exists();
    }

    public function getEmailVerifiedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('panel.date_format') . ' ' . config('panel.time_format')) : null;
    }

    /**
     * Retrieve all permissions of the user.
     *
     * @return array
     */
    public function getPermissionsAttribute(): array
    {
        $roles = $this->roles->pluck('id')->toArray();

        $permissions = Permission::select('title')
            ->join('permission_role AS pr', 'pr.permission_id', 'permissions.id')
            ->whereIn('pr.role_id', $roles)
            ->get()
            ->pluck('title')
            ->toArray();

        $roleBasedPermissions = [];

        foreach ($permissions as $permission) {
            $roleBasedPermissions[] = $this->getPermissions($permission);
        }

        return array_merge(...$roleBasedPermissions);
    }

    /*******************************************
     * E N D  C U S T O M  P R O P E R T I E S *
     ******************************************/

    public function userUserAlerts()
    {
        return $this->belongsToMany(UserAlert::class);
    }

    public function setEmailVerifiedAtAttribute($value)
    {
        $this->attributes['email_verified_at'] = $value ? Carbon::createFromFormat(config('panel.date_format') . ' ' . config('panel.time_format'), $value)->format('Y-m-d H:i:s') : null;
    }

    public function setPasswordAttribute($input)
    {
        if ($input) {
            $this->attributes['password'] = app('hash')->needsRehash($input) ? Hash::make($input) : $input;
        }
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    /**
     * Get employee of this user
     *
     * @return HasOne
     */
    public function employee()
    {
        return $this->hasOne(Employee::class, 'user_id');
    }

    /**
     * Get the qc faults created by this user
     *
     * @return HasMany
     */
    public function qcFaults(): HasMany
    {
        return $this->hasMany(QcFault::class);
    }

    /*********************
    * F U N C T I O N S  *
    *********************/

    /**
     * Retrieve all users where role === Admin
     *
     * @return Collection
     */
    public function getUserAdmins(): Collection
    {
        return self::join('role_user AS ru', 'ru.user_id', 'users.id')
            ->join('roles', 'roles.id', 'ru.role_id')
            ->where('roles.title', 'Admin')
            ->get();
    }

    /**
     * Retrieve all emails of users where role = Admin
     *
     * @return Collection
     */
    public function getUserAdminsWithValidEmails(): Collection
    {
        return $this->getUserAdmins()
            ->filter(function ($user, $key) {
                return filter_var($user['email'], FILTER_VALIDATE_EMAIL);
            });
    }

    /**
     * Retrieve list of permissions of the current user.
     * This is useful to our Vue application if we want to
     * implement permissions in that level.
     *
     * @param string $args
     *
     * @return array
     */
    public function getPermissions(string ...$permissions): array
    {
        $can = [];

        // loop through any given permissions
        foreach ($permissions as $permission) {
            $can[$permission] = Gate::allows($permission);
        }

        return $can;
    }

    /**
     * Retrieve list of permissions of the current user by module name.
     * This is useful when you want to retrieve permissions by module name.
     * This will automatically generate permission names for that given module ie. user_access.
     * Commonly used in our frontend when we want to have permission checking in that level.
     *
     * @param string $args
     *
     * @return array
     */
    public function getPermissionNameByModule(string $moduleName): array
    {
        // list of actions that the application permission supports
        $actions = ['access', 'create', 'edit', 'show', 'delete', 'status_change'];

        $permissionNames = [];

        // loop through the supported actions to create the permission name ie. user_access
        foreach ($actions as $action) {
            // build the permission name
            $name = "{$moduleName}_$action";

            // do permission check
            $permissionNames[$name] = Gate::allows($name);
        }

        return $permissionNames;
    }

    /**
     * Assign the user to a specific role based on the given role name
     *
     * @param string $title
     *
     * @return bool
     */
    public function assignToRoleByTitle(string $title): bool
    {
        // search for a role with the given title
        $employeeRole = Role::where('title', $title)->first();

        // checks if the provided role is not present
        if (!$employeeRole) {
            Log::info("No role title {$title} found.");

            return false;
        }

        // assign the role to the user
        $this->roles()->sync([$employeeRole->id]);

        return true;
    }

    /**
     * Check if user has Privacy Mode on
     *
     * @return bool
     */
    public function checkPrivacy(): bool
    {
        return Gate::allows('privacy');
    }
    /****************************
    * F U N C T I O N S  E N D *
    ****************************/
}
