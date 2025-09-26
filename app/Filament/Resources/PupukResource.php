<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PupukResource\Pages;
use App\Filament\Resources\PupukResource\RelationManagers;
use App\Models\Pupuk;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PupukResource extends Resource
{
    protected static ?string $model = Pupuk::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Utama')
                    ->description('Isi data pokok mengenai pupuk.')
                    ->icon('heroicon-o-information-circle')
                    ->collapsible()
                    ->schema([
                        Grid::make(2)->schema([
                            Forms\Components\TextInput::make('nama')
                                ->label('Nama Pupuk')
                                ->prefixIcon('heroicon-o-tag')
                                ->required()
                                ->maxLength(60)
                                ->helperText('Masukkan nama unik untuk pupuk ini.'),

                            Forms\Components\Select::make('jenis_id')
                                ->label('Jenis')
                                ->relationship('jenis', 'nama')
                                ->searchable()
                                ->native(false)
                                ->prefixIcon('heroicon-o-beaker')
                                ->required()
                                ->helperText('Pilih jenis pupuk.'),
                        ]),

                        Grid::make(2)->schema([
                            Forms\Components\Select::make('satuan_id')
                                ->label('Satuan')
                                ->relationship('satuan', 'nama')
                                ->searchable()
                                ->native(false)
                                ->preload()
                                ->prefixIcon('heroicon-o-scale')
                                ->required()
                                ->helperText('Pilih satuan yang sesuai.'),

                            Forms\Components\Select::make('perusahaan_id')
                                ->label('Perusahaan')
                                ->relationship('perusahaan', 'nama')
                                ->searchable()
                                ->native(false)
                                ->prefixIcon('heroicon-o-building-office')
                                ->required()
                                ->helperText('Pilih perusahaan pemasok pupuk.'),
                        ]),
                    ]),

                Section::make('Detail Stok & Harga')
                    ->description('Pengaturan jumlah stok, harga, dan keterangan tambahan.')
                    ->icon('heroicon-o-currency-dollar')
                    ->collapsible()
                    ->schema([
                        Grid::make(3)->schema([
                            Forms\Components\TextInput::make('stok')
                                ->label('Stok')
                                ->numeric()
                                ->default(0)
                                ->prefixIcon('heroicon-o-archive-box')
                                ->required(),

                            Forms\Components\TextInput::make('harga')
                                ->label('Harga')
                                ->numeric()
                                ->prefixIcon('heroicon-o-banknotes')
                                ->required(),

                            Forms\Components\Toggle::make('is_active')
                                ->label('Aktif?')
                                ->helperText('Matikan jika produk tidak lagi tersedia.')
                                ->inline(false),
                        ]),

                        Forms\Components\Textarea::make('deskripsi')
                            ->label('Deskripsi')
                            ->rows(3)
                            ->helperText('Tuliskan detail deskripsi pupuk.')
                            ->columnSpanFull()
                            ->required(),
                    ]),

                Section::make('Waktu Input')
                    ->description('Informasi kapan data dibuat & diubah.')
                    ->collapsible()
                    ->schema([
                        Grid::make(2)->schema([
                            Placeholder::make('created_at')
                                ->label('Dibuat pada')
                                ->content(fn($record) => $record?->created_at?->format('d M Y H:i') ?? '-'),

                            Placeholder::make('updated_at')
                                ->label('Terakhir diperbarui')
                                ->content(fn($record) => $record?->updated_at?->format('d M Y H:i') ?? '-'),
                        ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-tag')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('jenis.nama')
                    ->label('Jenis')
                    ->sortable()
                    ->icon('heroicon-o-beaker'),

                Tables\Columns\TextColumn::make('satuan.nama')
                    ->label('Satuan')
                    ->sortable()
                    ->icon('heroicon-o-scale'),

                Tables\Columns\TextColumn::make('perusahaan.nama')
                    ->label('Perusahaan')
                    ->sortable()
                    ->icon('heroicon-o-building-office'),

                Tables\Columns\TextColumn::make('stok')
                    ->label('Stok')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn($state) => $state > 50 ? 'success' : ($state > 10 ? 'warning' : 'danger')),

                Tables\Columns\TextColumn::make('harga')
                    ->label('Harga')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diubah')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('jenis_id')
                    ->label('Jenis')
                    ->relationship('jenis', 'nama')
                    ->searchable()
                    ->native(false),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif')
                    ->trueLabel('Aktif')
                    ->falseLabel('Non-Aktif')
                    ->placeholder('Semua'),
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
            'index' => Pages\ListPupuks::route('/'),
            'create' => Pages\CreatePupuk::route('/create'),
            'view' => Pages\ViewPupuk::route('/{record}'),
            'edit' => Pages\EditPupuk::route('/{record}/edit'),
        ];
    }
}