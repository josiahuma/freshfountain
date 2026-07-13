<?php

namespace App\Http\Controllers;

use App\Models\CalendarEvent;
use App\Services\CalendarEventService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Throwable;

class CalendarController extends Controller
{
    public function __construct(
        private readonly CalendarEventService $calendarService
    ) {
    }

    public function index(): View
    {
        return view('calendar.index', [
            'categories' =>
                CalendarEvent::categoryOptions(),
        ]);
    }

    public function feed(
        Request $request
    ): JsonResponse {
        try {
            $rangeStart = CarbonImmutable::parse(
                $request->string('start')->toString()
                    ?: now()
                        ->startOfMonth()
                        ->toIso8601String()
            );

            $rangeEnd = CarbonImmutable::parse(
                $request->string('end')->toString()
                    ?: now()
                        ->endOfMonth()
                        ->addDay()
                        ->toIso8601String()
            );
        } catch (Throwable) {
            return response()->json([
                'message' =>
                    'The supplied calendar date range is invalid.',
            ], 422);
        }

        if (
            $rangeEnd->lessThanOrEqualTo(
                $rangeStart
            )
        ) {
            return response()->json([
                'message' =>
                    'The calendar end date must be after its start date.',
            ], 422);
        }

        $category = $request
            ->string('category')
            ->toString();

        $events = $this->calendarService
            ->occurrences(
                $rangeStart,
                $rangeEnd,
                filled($category)
                    ? $category
                    : null
            );

        return response()->json(
            $events->values()
        );
    }

   /**
     * Browser-friendly printable calendar.
     *
     * Supported layouts:
     * - grid: A4 landscape month grid
     * - list: A4 portrait chronological event list
     */
    public function print(Request $request): View
    {
        $month = $this->resolveMonth($request);

        $layout = $this->resolvePrintLayout(
            $request
        );

        $calendarData = $layout === 'list'
            ? $this->calendarService
                ->printableListMonth($month)
            : $this->calendarService
                ->printableMonth($month);

        return view(
            $layout === 'list'
                ? 'calendar.print-list'
                : 'calendar.print',
            array_merge(
                $calendarData,
                [
                    'isPdf' => false,
                    'layout' => $layout,
                    'logoDataUri' =>
                        $this->logoDataUri(),
                ]
            )
        );
    }

    /**
     * Download the selected calendar layout as PDF.
     */
    public function pdf(Request $request): Response
    {
        $month = $this->resolveMonth($request);

        $layout = $this->resolvePrintLayout(
            $request
        );

        $calendarData = $layout === 'list'
            ? $this->calendarService
                ->printableListMonth($month)
            : $this->calendarService
                ->printableMonth($month);

        $data = array_merge(
            $calendarData,
            [
                'isPdf' => true,
                'layout' => $layout,
                'logoDataUri' =>
                    $this->logoDataUri(),
            ]
        );

        $filename = sprintf(
            'fresh-fountain-calendar-%s-%s.pdf',
            $month->format('Y-m'),
            $layout
        );

        return Pdf::loadView(
            $layout === 'list'
                ? 'calendar.print-list'
                : 'calendar.print',
            $data
        )
            ->setPaper(
                'a4',
                $layout === 'list'
                    ? 'portrait'
                    : 'landscape'
            )
            ->download($filename);
    }

    private function resolveMonth(
        Request $request
    ): CarbonImmutable {
        $value = $request
            ->string('month')
            ->toString();

        if (blank($value)) {
            return CarbonImmutable::now()
                ->startOfMonth();
        }

        try {
            return CarbonImmutable::createFromFormat(
                'Y-m',
                $value
            )->startOfMonth();
        } catch (Throwable) {
            abort(
                422,
                'The month must use YYYY-MM format.'
            );
        }
    }

    /**
     * Embed the logo directly so Dompdf does not need remote access.
     *
     * Add or change candidate paths to match the existing site logo.
     */
    private function logoDataUri(): ?string
    {
        $candidates = [
            public_path(
                'images/fresh-fountain-logo.png'
            ),
            public_path('images/logo.png'),
            public_path('logo.png'),
        ];

        foreach ($candidates as $path) {
            if (! is_file($path)) {
                continue;
            }

            $mime = mime_content_type($path)
                ?: 'image/png';

            return sprintf(
                'data:%s;base64,%s',
                $mime,
                base64_encode(
                    file_get_contents($path)
                )
            );
        }

        return null;
    }

    /**
     * Resolve the requested printable format.
     */
    private function resolvePrintLayout(
        Request $request
    ): string {
        $layout = strtolower(
            $request
                ->string('layout')
                ->toString()
        );

        return in_array(
            $layout,
            ['grid', 'list'],
            true
        )
            ? $layout
            : 'grid';
    }
}