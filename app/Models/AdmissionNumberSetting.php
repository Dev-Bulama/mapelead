<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdmissionNumberSetting extends Model
{
    protected $fillable = [
        'prefix', 'separator', 'include_year', 'include_course_code',
        'digit_length', 'reset_yearly', 'last_sequential_number', 'last_year',
    ];

    protected $casts = [
        'include_year' => 'boolean',
        'include_course_code' => 'boolean',
        'reset_yearly' => 'boolean',
    ];

    public static function instance(): self
    {
        return static::firstOrCreate([], [
            'prefix' => 'MAP',
            'separator' => '/',
            'include_year' => true,
            'include_course_code' => false,
            'digit_length' => 4,
            'reset_yearly' => true,
            'last_sequential_number' => 0,
        ]);
    }

    public function generateNumber(?string $courseCode = null): string
    {
        $currentYear = date('Y');

        if ($this->reset_yearly && $this->last_year !== (int)$currentYear) {
            $this->last_sequential_number = 0;
            $this->last_year = (int)$currentYear;
        }

        $this->last_sequential_number++;
        $this->save();

        $parts = [$this->prefix];

        if ($this->include_course_code && $courseCode) {
            $parts[] = strtoupper($courseCode);
        }

        if ($this->include_year) {
            $parts[] = $currentYear;
        }

        $parts[] = str_pad($this->last_sequential_number, $this->digit_length, '0', STR_PAD_LEFT);

        return implode($this->separator, $parts);
    }

    public function previewFormat(?string $courseCode = null): string
    {
        $year = date('Y');
        $parts = [$this->prefix];

        if ($this->include_course_code && $courseCode) {
            $parts[] = strtoupper($courseCode);
        }

        if ($this->include_year) {
            $parts[] = $year;
        }

        $parts[] = str_pad(1, $this->digit_length, '0', STR_PAD_LEFT);

        return implode($this->separator, $parts);
    }
}
