<?php

namespace App\Filament\Pages;

use Actions\Action;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Actions;
use Filament\Notifications\Notification;

class Profile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.profile';
    public ?array $data = [];


    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public function mount(): void
    {
        $this->form->fill(
            auth()->user()->only(['name', 'email'])
        );
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Full Name')
                    ->autofocus()
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->label('Email Address')
                    ->email()
                    ->required()
                    ->unique('users', 'email',  auth()->user()),

                TextInput::make('password')
                    ->label('New Password')
                    ->password()
                    ->nullable()
                    ->minLength(8),

                TextInput::make('password_confirmation')
                    ->label('Confirm Password')
                    ->password()
                    ->nullable()
                    ->same('password'),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('Update')
                ->color('primary')
                ->submit('Update'),
        ];
    }

    public function update(): void
    {
        auth()->user()->update(
            $this->form->getState()
        );

        Notification::make()
            ->title('Profile updated!')
            ->success()
            ->send();
    }
}
