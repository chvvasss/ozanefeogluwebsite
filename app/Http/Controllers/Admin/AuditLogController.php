<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class AuditLogController extends Controller
{
    public function __invoke(Request $request): View
    {
        $logs = Activity::query()
            ->with('causer')
            ->when($request->string('event')->value(), fn ($q, $event) => $q->where('event', $event))
            ->when($request->string('log')->value(), fn ($q, $log) => $q->where('log_name', $log))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.audit-log.index', ['logs' => $logs]);
    }
}
