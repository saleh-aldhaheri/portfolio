<?php

namespace App\Services;

use App\Models\AnalyticsLog;
use App\Models\ContactMessage;
use App\Models\Message;
use App\Models\Visitor;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    private const TTL_MINUTES = 5;

    /**
     * Relative periods for the dashboard filter.
     *
     * @var array<string, string>
     */
    public const PERIODS = [
        'today' => 'Today',
        'week' => 'This week',
        'month' => 'This month',
        'year' => 'This year',
    ];

    public const DEFAULT_PERIOD = 'month';

    /**
     * @return array{visitors: int, contact_messages: int, events: int, messages: int}
     */
    public static function overview(?string $period = null): array
    {
        return self::cached('overview', $period, function (string $period): array {
            return [
                'visitors' => self::scoped(Visitor::query(), $period)->count(),
                'contact_messages' => self::scoped(ContactMessage::query(), $period)->count(),
                'events' => self::scoped(AnalyticsLog::query(), $period)->count(),
                'messages' => self::scoped(Message::query(), $period)->count(),
            ];
        });
    }

    /**
     * @return array<int, array{name: string, total: int}>
     */
    public static function countries(?string $period = null): array
    {
        return self::cached('countries', $period, function (string $period): array {
            return self::scoped(Visitor::query(), $period)
                ->select('country', DB::raw('COUNT(*) as total'))
                ->whereNotNull('country')
                ->groupBy('country')
                ->orderByDesc('total')
                ->limit(10)
                ->get()
                ->map(fn ($row): array => ['name' => $row->country, 'total' => (int) $row->total])
                ->all();
        });
    }

    /**
     * @return array<int, array{name: string, total: int}>
     */
    public static function cities(?string $period = null): array
    {
        return self::cached('cities', $period, function (string $period): array {
            return self::scoped(Visitor::query(), $period)
                ->select('city', DB::raw('COUNT(*) as total'))
                ->whereNotNull('city')
                ->groupBy('city')
                ->orderByDesc('total')
                ->limit(10)
                ->get()
                ->map(fn ($row): array => ['name' => $row->city, 'total' => (int) $row->total])
                ->all();
        });
    }

    /**
     * @return array<int, array{name: string, total: int}>
     */
    public static function messagesByCountry(?string $period = null): array
    {
        return self::cached('messages_by_country', $period, function (string $period): array {
            [$start, $end] = self::range($period);

            return Message::query()
                ->join('visitors', 'visitors.id', '=', 'messages.visitor_id')
                ->select('visitors.country', DB::raw('COUNT(*) as total'))
                ->whereNotNull('visitors.country')
                ->when($start, fn (Builder $query): Builder => $query->where('messages.created_at', '>=', $start))
                ->when($end, fn (Builder $query): Builder => $query->where('messages.created_at', '<=', $end))
                ->groupBy('visitors.country')
                ->orderByDesc('total')
                ->limit(10)
                ->get()
                ->map(fn ($row): array => ['name' => $row->country, 'total' => (int) $row->total])
                ->all();
        });
    }

    /**
     * @return array<int, array{event: string, total: int}>
     */
    public static function topEvents(?string $period = null): array
    {
        return self::cached('top_events', $period, function (string $period): array {
            return self::scoped(AnalyticsLog::query(), $period)
                ->select('event', DB::raw('COUNT(*) as total'))
                ->groupBy('event')
                ->orderByDesc('total')
                ->limit(10)
                ->get()
                ->map(fn ($row): array => ['event' => $row->event, 'total' => (int) $row->total])
                ->all();
        });
    }

    /**
     * @return array<int, array{uuid: string, name: string|null, email: string|null, events: int}>
     */
    public static function eventsPerUser(?string $period = null): array
    {
        return self::cached('events_per_user', $period, function (string $period): array {
            return self::scoped(AnalyticsLog::query(), $period)
                ->select('visitor_id', DB::raw('COUNT(*) as total'))
                ->with('visitor:id,uuid,name,email')
                ->groupBy('visitor_id')
                ->orderByDesc('total')
                ->limit(20)
                ->get()
                ->map(fn ($row): array => [
                    'uuid' => $row->visitor?->uuid ?? 'unknown',
                    'name' => $row->visitor?->name,
                    'email' => $row->visitor?->email,
                    'events' => (int) $row->total,
                ])
                ->all();
        });
    }

    /**
     * Time spent per visitor, derived from the span of their tracked events.
     *
     * @return array<int, array{uuid: string, first_activity: string, last_activity: string, duration: string, duration_seconds: int}>
     */
    public static function timeSpent(?string $period = null): array
    {
        return self::cached('time_spent', $period, function (string $period): array {
            return self::scoped(AnalyticsLog::query(), $period)
                ->select('visitor_id', DB::raw('MIN(created_at) as first_activity'), DB::raw('MAX(created_at) as last_activity'))
                ->with('visitor:id,uuid')
                ->groupBy('visitor_id')
                ->get()
                ->map(function ($row): array {
                    $first = Carbon::parse($row->first_activity);
                    $last = Carbon::parse($row->last_activity);
                    $seconds = (int) abs($first->diffInSeconds($last));

                    return [
                        'uuid' => $row->visitor?->uuid ?? 'unknown',
                        'first_activity' => $first->format('Y-m-d H:i'),
                        'last_activity' => $last->format('Y-m-d H:i'),
                        'duration' => self::formatDuration($seconds),
                        'duration_seconds' => $seconds,
                    ];
                })
                ->sortByDesc('duration_seconds')
                ->values()
                ->take(20)
                ->all();
        });
    }

    /**
     * @return array<int, array{uuid: string, name: string|null, email: string|null, messages: int}>
     */
    public static function formsPerUser(?string $period = null): array
    {
        return self::cached('forms_per_user', $period, function (string $period): array {
            return self::scoped(ContactMessage::query(), $period)
                ->select('visitor_id', DB::raw('COUNT(*) as total'))
                ->with('visitor:id,uuid,name,email')
                ->groupBy('visitor_id')
                ->orderByDesc('total')
                ->get()
                ->map(fn ($row): array => [
                    'uuid' => $row->visitor?->uuid ?? 'unknown',
                    'name' => $row->visitor?->name,
                    'email' => $row->visitor?->email,
                    'messages' => (int) $row->total,
                ])
                ->all();
        });
    }

    /**
     * @return array<int, array{label: string, value: int}>
     */
    public static function engagement(?string $period = null): array
    {
        return self::cached('engagement', $period, function (string $period): array {
            $counts = self::scoped(AnalyticsLog::query(), $period)
                ->select('event', DB::raw('COUNT(*) as total'))
                ->groupBy('event')
                ->pluck('total', 'event');

            $linkedIn = ($counts['open-linked'] ?? 0) + ($counts['open-linkedin'] ?? 0);

            return [
                ['label' => 'Resume opened', 'value' => (int) ($counts['open-resume'] ?? 0)],
                ['label' => 'GitHub clicked', 'value' => (int) ($counts['open-github'] ?? 0)],
                ['label' => 'LinkedIn clicked', 'value' => (int) $linkedIn],
                ['label' => 'Email clicked', 'value' => (int) ($counts['open-email'] ?? 0)],
                ['label' => 'Forms submitted', 'value' => self::scoped(ContactMessage::query(), $period)->count()],
            ];
        });
    }

    /**
     * Filter options for the dashboard: relative periods plus specific years.
     * Cached per calendar year so the list rolls over on January 1st.
     *
     * @return array<string, string>
     */
    public static function filterOptions(): array
    {
        return self::PERIODS;
    }

    /**
     * @template T
     *
     * @param  Closure(string): T  $callback
     * @return T
     */
    private static function cached(string $metric, ?string $period, Closure $callback): mixed
    {
        // Only known periods may create cache keys; anything else falls back to the default.
        $period = array_key_exists($period ?? '', self::filterOptions())
            ? $period
            : self::DEFAULT_PERIOD;

        return Cache::remember(
            "analytics.{$metric}:{$period}",
            now()->addMinutes(self::TTL_MINUTES),
            fn (): mixed => $callback($period)
        );
    }

    private static function scoped(Builder $query, string $period): Builder
    {
        [$start, $end] = self::range($period);

        return $query
            ->when($start, fn (Builder $q): Builder => $q->where('created_at', '>=', $start))
            ->when($end, fn (Builder $q): Builder => $q->where('created_at', '<=', $end));
    }

    /**
     * @return array{0: Carbon|null, 1: Carbon|null}
     */
    private static function range(string $period): array
    {
        if (preg_match('/^\d{4}$/', $period)) {
            $year = (int) $period;

            return [
                Carbon::createFromDate($year, 1, 1)->startOfDay(),
                Carbon::createFromDate($year, 12, 31)->endOfDay(),
            ];
        }

        $start = match ($period) {
            'today' => now()->startOfDay(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'year' => now()->startOfYear(),
            default => now()->startOfMonth(),
        };

        return [$start, null];
    }

    private static function formatDuration(int $seconds): string
    {
        if ($seconds < 60) {
            return "{$seconds}s";
        }

        $minutes = (int) floor($seconds / 60);

        if ($minutes < 60) {
            return "{$minutes}m ".($seconds % 60).'s';
        }

        $hours = (int) floor($minutes / 60);

        return "{$hours}h ".($minutes % 60).'m';
    }
}
