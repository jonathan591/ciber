<?php

namespace App\Filament\Pages\Auth;
 
use Coderflex\FilamentTurnstile\Forms\Components\Turnstile;
use Filament\Forms\Form;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Pages\Auth\Login as AuthLogin;
 
class Login extends AuthLogin
{
    /**
     * @return array<int|string, string|Form>
     */
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getEmailFormComponent(),
                        $this->getPasswordFormComponent(),
                        // $this->getRememberFormComponent(),
                        Turnstile::make('captcha')
                        ->language('es-es') // see below
                            // ->label('Captcha')
                            ->theme('light'),
                    ])
                    ->statePath('data'),
            ),
        ];
    }
 
    // if you want to reset the captcha in case of validation error
    protected function throwFailureValidationException(): never
    {
        $this->dispatch('reset-captcha');
 
        parent::throwFailureValidationException();
    }
}