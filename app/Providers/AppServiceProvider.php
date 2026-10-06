<?php

namespace App\Providers;

use Closure;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Table;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Table::configureUsing(
            fn (Table $table): Table => $table->defaultNumberLocale('id'),
        );

        /*
         * Money input that displays the value using Indonesian thousands
         * separators (dots) while still storing a plain number.
         */
        TextInput::macro('money', function (): TextInput {
            /** @var TextInput $field */
            $field = $this;

            return $field
                ->inputMode('decimal')
                ->formatStateUsing(fn ($state): ?string => ($state === null || $state === '') ? null : number_format((float) preg_replace('/[^0-9-]/', '', (string) $state), 0, ',', '.'))
                ->dehydrateStateUsing(fn ($state): float => (float) preg_replace('/[^0-9-]/', '', (string) $state))
                ->rule(fn (): Closure => function (string $attribute, mixed $value, Closure $fail): void {
                    if (filled($value) && ! is_numeric(preg_replace('/[^0-9-]/', '', (string) $value))) {
                        $fail('The :attribute field must be a number.');
                    }
                });
        });
    }
}
