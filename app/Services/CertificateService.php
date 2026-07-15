<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\CourseEnrollment;
use App\Models\Page;
use Barryvdh\DomPDF\Facade\Pdf;
use chillerlan\QRCode\QRCode;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class CertificateService
{
    public function __construct(
        private readonly LearningEmailService $learningEmailService
    ) {
    }

    public function issueForEnrollment(
        CourseEnrollment $enrollment,
        ?int $issuedBy = null
    ): Certificate {
        $enrollment->loadMissing([
            'user',
            'course',
        ]);

        if (
            $enrollment->status
            !== CourseEnrollment::STATUS_COMPLETED
            || $enrollment->progress_percentage < 100
        ) {
            throw new RuntimeException(
                'A certificate can only be issued for a completed course.'
            );
        }

        $certificate = Certificate::query()
            ->firstOrCreate(
                [
                    'course_enrollment_id' =>
                        $enrollment->id,
                ],
                [
                    'user_id' =>
                        $enrollment->user_id,

                    'course_id' =>
                        $enrollment->course_id,

                    'issued_by' =>
                        $issuedBy,

                    'student_name' =>
                        $enrollment->user->name,

                    'course_title' =>
                        $enrollment->course->title,

                    'issued_at' =>
                        $enrollment->completed_at
                        ?? now(),

                    'metadata' => [
                        'progress_percentage' =>
                            $enrollment
                                ->progress_percentage,

                        'completed_at' =>
                            optional(
                                $enrollment->completed_at
                            )?->toIso8601String(),
                    ],
                ]
            );

        $this->ensurePdfExists(
            $certificate
        );

        $certificate = $certificate->refresh();

        $this->learningEmailService
            ->queueCertificateReady(
                $certificate
            );

        return $certificate;
    }

    public function ensurePdfExists(
        Certificate $certificate
    ): string {
        if (
            filled($certificate->pdf_path)
            && Storage::disk('local')->exists(
                $certificate->pdf_path
            )
        ) {
            return $certificate->pdf_path;
        }

        return $this->generatePdf(
            $certificate
        );
    }

    public function generatePdf(
        Certificate $certificate
    ): string {
        $certificate->loadMissing([
            'user',
            'course',
            'enrollment',
        ]);

        $verificationUrl = route(
            'certificates.verify',
            [
                'verificationCode' =>
                    $certificate
                        ->verification_code,
            ]
        );

        $qrCodeDataUri = (
            new QRCode()
        )->render($verificationUrl);

        $logoDataUri =
            $this->resolveLogoDataUri();

        $pdf = Pdf::loadView(
            'certificates.pdf',
            [
                'certificate' => $certificate,
                'verificationUrl' => $verificationUrl,
                'qrCodeDataUri' => $qrCodeDataUri,
                'logoDataUri' => $logoDataUri,
            ]
        )
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
                'defaultFont' => 'DejaVu Sans',
                'dpi' => 96,
            ]);

        $path = sprintf(
            'certificates/%d/%s.pdf',
            $certificate->user_id,
            Str::slug(
                $certificate
                    ->certificate_number
            )
        );

        Storage::disk('local')->put(
            $path,
            $pdf->output()
        );

        $certificate->update([
            'pdf_path' => $path,
        ]);

        return $path;
    }

    public function regeneratePdf(
        Certificate $certificate
    ): string {
        if (
            filled($certificate->pdf_path)
            && Storage::disk('local')->exists(
                $certificate->pdf_path
            )
        ) {
            Storage::disk('local')->delete(
                $certificate->pdf_path
            );
        }

        $certificate->update([
            'pdf_path' => null,
        ]);

        return $this->generatePdf(
            $certificate->fresh()
        );
    }

    private function resolveLogoDataUri(): ?string
    {
        $homePage = Page::query()
            ->where('slug', 'home')
            ->first();

        $logoPath = $homePage
            ? data_get(
                $homePage->sections,
                'header.logo'
            )
            : null;

        if (blank($logoPath)) {
            return null;
        }

        $disk = Storage::disk('public');

        if (! $disk->exists($logoPath)) {
            return null;
        }

        $mimeType =
            $disk->mimeType($logoPath)
            ?: 'image/png';

        return sprintf(
            'data:%s;base64,%s',
            $mimeType,
            base64_encode(
                $disk->get($logoPath)
            )
        );
    }
}