<?php

namespace App\Models;

use \DateTimeInterface;
use Carbon\Carbon;
use Hash;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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

    public $table = 'users';

    protected $hidden = [
        'remember_token',
        'password',
    ];

    protected $dates = [
        'email_verified_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

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

    public function getIsAdminAttribute()
    {
        return $this->roles()->where('id', 1)->exists();
    }

    public function userUserAlerts()
    {
        return $this->belongsToMany(UserAlert::class);
    }

    public function getEmailVerifiedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('panel.date_format') . ' ' . config('panel.time_format')) : null;
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

    /*********************
    * F U N C T I O N S  *
    *********************/

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
     * Retrieve list of permissions of the current user by module names.
     * This is useful when you want to retrieve permissions of different modules ie. users, settings.
     * This will automatically generate permission names for that given module ie. user_access.
     * Typically used in our frontend when we want to have permission checking in that level.
     *
     * @param string $args
     *
     * @return array
     */
    public function getPermissionsPerModules(string ...$args)
    {
        $permissions = [];

        // loop through the passed module names
        // Note* it will not check if the permission exist or not, it will just check if
        // the current user have permission to that name permission
        foreach ($args as $arg) {
            // build the permission module names
            $permissions = array_merge($permissions, $this->getPermissionNameByModule($arg));
        }

        return $permissions;
    }

    /**
     * Retrieve list of permissions of the current user by module name.
     * This is useful when you want to retrieve permissions by module name.
     * This will automatically generate permission names for that given module ie. user_access.
     * Typically used in our frontend when we want to have permission checking in that level.
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

        // checks if the provided role is present
        if (!$employeeRole) {
            Log::info("No role title {$title} found.");
            return false;
        }

        // assign the role to the user
        $this->roles()->sync([$employeeRole->id]);

        return true;
    }
    /****************************
    * F U N C T I O N S  E N D *
    ****************************/
}
