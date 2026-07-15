<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_enrollment_id',
        'user_id',
        'course_id',
        'issued_by',
        'certificate_number',
        'verification_code',
        'student_name',
        'course_title',
        'issued_at',
        'pdf_path',
        'revoked_at',
        'revocation_reason',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
            'revoked_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Certificate $certificate): void {
            if (blank($certificate->verification_code)) {
                $certificate->verification_code =
                    (string) Str::uuid();
            }

            if (blank($certificate->certificate_number)) {
                $certificate->certificate_number =
                    static::generateCertificateNumber();
            }
        });
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(
            CourseEnrollment::class,
            'course_enrollment_id'
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function issuedByUser(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'issued_by'
        );
    }

    public function isRevoked(): bool
    {
        return $this->revoked_at !== null;
    }

    public function isValid(): bool
    {
        return ! $this->isRevoked();
    }

    public function getDisplayNumberAttribute(): string
    {
        return $this->certificate_number;
    }

    private static function generateCertificateNumber(): string
    {
        do {
            $number = sprintf(
                'FFCN-%s-%06d',
                now()->format('Y'),
                random_int(1, 999999)
            );
        } while (
            static::query()
                ->where('certificate_number', $number)
                ->exists()
        );

        return $number;
    }
}