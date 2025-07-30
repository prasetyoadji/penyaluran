<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JenisResource\Pages;
use App\Filament\Resources\JenisResource\RelationManagers;
use App\Models\Jenis;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class JenisResource extends Resource
{
    protected static ?string $model = Jenis::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Jenis')
                    ->description('Tambahkan jenis baru agar data produk dapat dikelompokkan dengan baik.')
                    ->schema([
                        Forms\Components\TextInput::make('nama')
                            ->label('Nama Jenis')
                            ->placeholder('Contoh: Organik, Anorganik')
                            ->required()
                            ->maxLength(15)
                            ->prefixIcon('heroicon-o-tag') // Ikon sesuai jenis/kategori
                            ->helperText('Masukkan nama jenis, maksimal 15 karakter.')
                            ->extraInputAttributes(['class' => 'text-lg font-semibold']), // Teks lebih menarik
                    ])
                    ->collapsible(), // Section dapat di-collapse
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageJenis::route('/'),
        ];
    }
}
