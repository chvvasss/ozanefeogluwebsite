<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserDevice;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SessionsController extends Controller
{
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();
        $devices = UserDevice::query()
            ->where('user_id', $user->id)
            ->orderByDesc('last_active_at')
            ->get();

        return view('admin.profile.sessions', [
            'devices' => $devices,
            'currentSessionId' => Session::getId(),
        ]);
    }

    public function destroy(Request $request, UserDevice $device): RedirectResponse
    {
        abort_unless($device->user_id === $request->user()->id, 403);

        if ($device->session_id === Session::getId()) {
            return back()->with('warning', __('Aktif oturumunuzu buradan kapatamazsınız; "Çıkış yap" düğmesini kullanın.'));
        }

        $device->delete();

        return back()->with('status', __('Oturum kapatıldı.'));
    }
}
