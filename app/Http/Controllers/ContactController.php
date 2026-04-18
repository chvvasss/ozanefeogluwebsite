<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ContactMessageRequest;
use App\Models\ContactMessage;
use App\Models\Page;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ContactController extends Controller
{
    public function show(): View
    {
        $page = $this->page();

        return view('public.pages.contact', ['page' => $page]);
    }

    public function send(ContactMessageRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Honeypot triggered → pretend success (don't reveal detection)
        if (! empty($request->input('website'))) {
            return redirect()
                ->to('/iletisim#tesekkur')
                ->with('contact_status', __('Mesajın alındı. 72 saat içinde dönüş yapılacak.'));
        }

        ContactMessage::create([
            'name'       => $data['name'],
            'email'      => $data['email'],
            'subject'    => $data['subject'] ?? null,
            'body'       => $data['body'],
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
            'status'     => 'new',
        ]);

        return redirect()
            ->to('/iletisim#tesekkur')
            ->with('contact_status', __('Mesajın alındı. 72 saat içinde dönüş yapılacak.'));
    }

    private function page(): Page
    {
        $page = Page::query()
            ->published()
            ->where('slug', 'iletisim')
            ->first();

        if (! $page) {
            throw new NotFoundHttpException;
        }

        return $page;
    }
}
