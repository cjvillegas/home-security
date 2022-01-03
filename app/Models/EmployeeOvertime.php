<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeOvertime extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * Mass assignable fields
     *
     * @var array
     */
    protected $fillable = [
        'overtime_booking_id',
        'employee_id',
        'shift',
        'department',
        'is_approved',
        'approved_at',
        'checked_by'
    ];

    /**
     * Relation to Overtime Booking
     *
     * @return BelongsTo
     */
    public function overtimeBooking()
    {
        return $this->belongsTo(OvertimeBooking::class);
    }

    /**
     * Relation to Employee
     *
     * @return BelongsTo
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Relation to user to get Approved By data
     *
     * @return void
     */
    public function checkedBy()
    {
        return $this->belongsTo(User::class, 'checked_by', 'id');
    }
}
