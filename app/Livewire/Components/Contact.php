<?php

namespace App\Livewire\Components;

use App\Models\Visitor;
use App\Services\ContactService;
use App\Services\VisitorService;
use Illuminate\Support\Facades\Cookie;
use Illuminate\View\View;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class Contact extends Component
{
    public string $name = '';

    public string $email = '';

    public string $message = '';

    public string $otp = '';

    public string $otpToken = '';

    public string $otpEmail = '';

    public bool $requireOtp = false;

    public bool $linkingEmail = false;

    public function submit(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $visitor = $this->resolveVisitor();

        $this->otpEmail = $visitor->email ?: $this->email;
        $this->linkingEmail = $visitor->email === null;

        $issued = app(ContactService::class)->issue([
            'name' => $this->name,
            'email' => $this->email,
            'message' => $this->message,
            'visitor_id' => $visitor->id,
            'verified_email' => $this->otpEmail,
        ]);

        $this->otpToken = $issued['token'];
        $this->requireOtp = true;
    }

    public function resend(): void
    {
        app(ContactService::class)->resend($this->otpToken);
    }

    public function confirmOtp(): void
    {
        $this->validate([
            'otp' => ['required', 'digits:6'],
        ]);

        $payload = app(ContactService::class)->verify($this->otpToken, $this->otp);

        if ($payload === null) {
            $this->addError('otp', 'Invalid or expired code.');

            return;
        }

        $visitor = Visitor::find($payload['visitor_id']);

        if ($visitor && $visitor->email === null) {
            $visitor->update(['email' => $payload['verified_email']]);
        }

        app(ContactService::class)->store($payload);

        $this->reset('name', 'email', 'message', 'otp', 'otpToken', 'otpEmail', 'requireOtp', 'linkingEmail');

        Toaster::success('message sent. i will reply soon.');
    }

    public function cancelOtp(): void
    {
        $this->reset('otp', 'otpToken', 'otpEmail', 'requireOtp', 'linkingEmail');
    }

    public function render(): View
    {
        return view('livewire.components.contact');
    }

    private function resolveVisitor(): Visitor
    {
        $request = request();

        $visitor = app(VisitorService::class)->resolve($request);

        if (! $request->cookie('visitor')) {
            Cookie::queue('visitor', $visitor->uuid, 60 * 24 * 365);
        }

        return $visitor;
    }
}
