<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'title',
        'description',
        'status',
        'due_date',
        'assigned_to',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELED = 'canceled';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_COMPLETED,
            self::STATUS_CANCELED,
        ];
    }

    protected $attributes = [
        'status' => self::STATUS_PENDING,
    ];

    protected $with = ['assignedUser' , 'dependencies'];

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function dependencies()
    {
        return $this->belongsToMany(
            Task::class,
            'task_dependencies',
            'task_id',
            'dependency_id'
        );
    }

    public function dependents()
    {
        return $this->belongsToMany(
            Task::class,
            'task_dependencies',
            'dependency_id',
            'task_id'
        );
    }
}
