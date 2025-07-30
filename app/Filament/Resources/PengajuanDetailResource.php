<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PengajuanDetailResource\Pages;
use App\Filament\Resources\PengajuanDetailResource\RelationManagers;
use App\Models\PengajuanDetail;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PengajuanDetailResource extends Resource
{
    protected static ?string $model = PengajuanDetail::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('pengajuan_id')
                    ->relationship('pengajuan', 'id')
                    ->required(),
                Forms\Components\Select::make('pupuk_id')
                    ->relationship('pupuk', 'id')
                    ->required(),
                Forms\Components\TextInput::make('qty')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('harga')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('pengajuan.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pupuk.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('qty')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('harga')
                    ->numeric()
                    ->sortable(),
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
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPengajuanDetails::route('/'),
            'create' => Pages\CreatePengajuanDetail::route('/create'),
            'view' => Pages\ViewPengajuanDetail::route('/{record}'),
            'edit' => Pages\EditPengajuanDetail::route('/{record}/edit'),
        ];
    }
}