<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Services\CertificateService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CertificateController extends Controller
{
    public function download(
        Certificate $certificate,
        CertificateService $certificateService
    ): StreamedResponse {
        abort_unless(
            $certificate->user_id
            === Auth::id(),
            403
        );

        abort_if(
            $certificate->isRevoked(),
            403,
            'This certificate has been revoked.'
        );

        $path =
            $certificateService
                ->ensurePdfExists(
                    $certificate
                );

        return Storage::disk('local')
            ->download(
                $path,
                $certificate
                    ->certificate_number
                . '.pdf',
                [
                    'Content-Type' =>
                        'application/pdf',
                ]
            );
    }

    public function stream(
        Certificate $certificate,
        CertificateService $certificateService
    ): Response {
        abort_unless(
            $certificate->user_id
            === Auth::id(),
            403
        );

        abort_if(
            $certificate->isRevoked(),
            403,
            'This certificate has been revoked.'
        );

        $path =
            $certificateService
                ->ensurePdfExists(
                    $certificate
                );

        return response(
            Storage::disk('local')->get(
                $path
            ),
            200,
            [
                'Content-Type' =>
                    'application/pdf',

                'Content-Disposition' =>
                    'inline; filename="'
                    . $certificate
                        ->certificate_number
                    . '.pdf"',
            ]
        );
    }

    public function verify(
        string $verificationCode
    ): View {
        $certificate = Certificate::query()
            ->with([
                'course',
                'user',
            ])
            ->where(
                'verification_code',
                $verificationCode
            )
            ->first();

        return view(
            'certificates.verify',
            [
                'certificate' =>
                    $certificate,

                'isValid' =>
                    $certificate
                    && ! $certificate
                        ->isRevoked(),
            ]
        );
    }
}