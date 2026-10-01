<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('name')
                    ->label('Nama User')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->revealable()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->helperText('Kosongkan bila tidak ingin mengganti password.'),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        1 => 'Superadmin',
                        2 => 'Admin',
                        3 => 'Admin Pusat',
                    ])
                    ->default(2)
                    ->required()
                    ->live(),
                Select::make('toko_id')
                    ->label('Toko')
                    ->relationship('toko', 'nama_toko')
                    ->wrapOptionLabels(false)->searchable()
                    ->preload()
                    ->visible(fn ($get): bool => (int) $get('status') === 2),
                CheckboxList::make('user_menu')
                    ->label('Menu')
                    ->options(self::menuOptions())
                    ->columns(3)
                    ->formatStateUsing(fn ($state): array => is_array($state)
                        ? $state
                        : array_values(array_filter(explode(',', (string) $state))))
                    ->dehydrateStateUsing(fn ($state): string => implode(',', (array) $state))
                    ->visible(fn ($get): bool => (int) $get('status') === 2)
                    ->columnSpanFull(),
            ]);
    }

    /**
     * Menu keys match the legacy `user_menu` values used across the application.
     *
     * @return array<string, string>
     */
    public static function menuOptions(): array
    {
        return [
            'Barang' => 'Barang',
            'Kategori Barang' => 'Kategori Barang',
            'Toko' => 'Toko',
            'Supplier' => 'Supplier',
            'Setting' => 'Accounting',
            'Barang Masuk' => 'Barang Masuk',
            'Barang Keluar' => 'Barang Keluar',
            'Stock' => 'Stock',
            'Pindah Toko' => 'Pindah Toko',
            'Search' => 'Search',
            'Purchase Order' => 'Purchase Order',
            'Penjualan' => 'Penjualan',
            'Invoice' => 'Invoice',
            'Quotation' => 'Quotation',
            'Report' => 'Report',
            'User' => 'User',
        ];
    }
}
