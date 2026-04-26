<?php

declare(strict_types=1);

use App\Models\ContactMessage;
use App\Models\Page;

beforeEach(function (): void {
    Page::query()->create([
        'slug' => 'iletisim',
        'kind' => 'system',
        'template' => 'contact',
        'title' => ['tr' => 'İletişim'],
        'body' => ['tr' => '<p>x</p>'],
        'extras' => ['channels' => []],
        'is_published' => true,
    ]);
});

it('stores a valid contact message', function () {
    $response = $this->post('/iletisim', [
        'name' => 'Ali Veli',
        'email' => 'ali@example.com',
        'subject' => 'Yayın teklifi',
        'body' => 'Sahada bir hikâye için sizinle çalışmak istiyoruz...',
    ]);

    $response->assertRedirect();
    expect(ContactMessage::count())->toBe(1);

    $message = ContactMessage::first();
    expect($message->name)->toBe('Ali Veli')
        ->and($message->email)->toBe('ali@example.com')
        ->and($message->status)->toBe('new');
});

it('rejects invalid form payload', function () {
    $response = $this->post('/iletisim', [
        'name' => '',
        'email' => 'not-an-email',
        'body' => 'kısa',
    ]);

    $response->assertSessionHasErrors(['name', 'email', 'body']);
    expect(ContactMessage::count())->toBe(0);
});

it('silently absorbs honeypot submissions', function () {
    $this->post('/iletisim', [
        'name' => 'Bot',
        'email' => 'bot@example.com',
        'body' => 'long enough body to pass minimum length',
        'website' => 'https://spam.example',
    ])->assertRedirect();

    expect(ContactMessage::count())->toBe(0);
});
