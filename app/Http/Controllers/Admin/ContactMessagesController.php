<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContactMessagesController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', ContactMessage::class);

        $messages = ContactMessage::query()
            ->when($request->string('status')->value(), function ($q, $status) {
                if (in_array($status, ContactMessage::STATUSES, true)) {
                    $q->where('status', $status);
                }
            })
            ->latest()
            ->paginate(25);

        $counts = [
            'new' => ContactMessage::query()->where('status', 'new')->count(),
            'read' => ContactMessage::query()->where('status', 'read')->count(),
            'replied' => ContactMessage::query()->where('status', 'replied')->count(),
            'spam' => ContactMessage::query()->where('status', 'spam')->count(),
        ];

        return view('admin.contact.index', [
            'messages' => $messages,
            'filter' => $request->string('status')->value(),
            'counts' => $counts,
        ]);
    }

    public function show(Request $request, ContactMessage $contactMessage): View
    {
        $this->authorize('view', $contactMessage);

        if ($contactMessage->status === 'new') {
            $contactMessage->forceFill([
                'status' => 'read',
                'read_at' => now(),
            ])->save();
        }

        return view('admin.contact.show', ['message' => $contactMessage]);
    }

    public function update(Request $request, ContactMessage $contactMessage): RedirectResponse
    {
        $this->authorize('update', $contactMessage);

        $status = (string) $request->input('status');
        abort_unless(in_array($status, ContactMessage::STATUSES, true), 422);

        $contactMessage->forceFill([
            'status' => $status,
            'read_at' => $status !== 'new' ? ($contactMessage->read_at ?? now()) : null,
        ])->save();

        return back()->with('status', __('Durum güncellendi.'));
    }

    public function destroy(Request $request, ContactMessage $contactMessage): RedirectResponse
    {
        $this->authorize('delete', $contactMessage);

        $contactMessage->delete();

        return redirect()
            ->route('admin.contact.index')
            ->with('status', __('Mesaj silindi.'));
    }
}
