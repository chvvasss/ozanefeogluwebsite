<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class AuditLogController extends Controller
{
    /**
     * Paginated activity log with filter support.
     *
     * Accepted query params:
     *   - subject (subject_type FQCN, e.g. App\Models\Writing)
     *   - causer  (user id)
     *   - event   (login.success, created, updated, deleted, ...)
     *   - log     (log_name: auth, default, ...)
     *   - from / to (date range, YYYY-MM-DD, filters created_at)
     *   - q       (free text search on description)
     *
     * All query params survive pagination via withQueryString().
     */
    public function __invoke(Request $request): View
    {
        $filters = [
            'subject' => $request->string('subject')->toString() ?: null,
            'causer' => $request->string('causer')->toString() ?: null,
            'event' => $request->string('event')->toString() ?: null,
            'log' => $request->string('log')->toString() ?: null,
            'from' => $request->string('from')->toString() ?: null,
            'to' => $request->string('to')->toString() ?: null,
            'q' => $request->string('q')->toString() ?: null,
        ];

        $logs = Activity::query()
            ->with('causer')
            ->when($filters['subject'], fn ($q, $v) => $q->where('subject_type', $v))
            ->when($filters['causer'], fn ($q, $v) => $q->where('causer_id', (int) $v))
            ->when($filters['event'], fn ($q, $v) => $q->where('event', $v))
            ->when($filters['log'], fn ($q, $v) => $q->where('log_name', $v))
            ->when($filters['from'], fn ($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($filters['to'], fn ($q, $v) => $q->whereDate('created_at', '<=', $v))
            ->when($filters['q'], fn ($q, $v) => $q->where('description', 'like', '%'.$v.'%'))
            ->latest()
            ->paginate(30)
            ->withQueryString();

        // Surface distinct values so the filter UI can offer real options without guesswork.
        $subjectTypes = Activity::query()
            ->select('subject_type')
            ->whereNotNull('subject_type')
            ->distinct()
            ->orderBy('subject_type')
            ->pluck('subject_type');

        $eventNames = Activity::query()
            ->select('event')
            ->whereNotNull('event')
            ->distinct()
            ->orderBy('event')
            ->pluck('event');

        $logNames = Activity::query()
            ->select('log_name')
            ->whereNotNull('log_name')
            ->distinct()
            ->orderBy('log_name')
            ->pluck('log_name');

        $causers = User::query()
            ->whereIn('id', Activity::query()->whereNotNull('causer_id')->distinct()->pluck('causer_id'))
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return view('admin.audit-log.index', [
            'logs' => $logs,
            'filters' => $filters,
            'subjectTypes' => $subjectTypes,
            'eventNames' => $eventNames,
            'logNames' => $logNames,
            'causers' => $causers,
        ]);
    }
}
