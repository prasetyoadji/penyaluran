<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PupukResource\Pages;
use App\Filament\Resources\PupukResource\RelationManagers;
use App\Models\Pupuk;
use Filament\Forms;
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
                Forms\Components\Section::make('Informasi Produk')
                    ->description('Isi data produk dengan lengkap dan benar.')
                    ->schema([
                        Forms\Components\Grid::make(2)->schema([ // Bagi jadi 2 kolom
                            Forms\Components\TextInput::make('nama')
                                ->label('Nama Produk')
                                ->placeholder('Contoh: Pupuk Urea')
                                ->maxLength(45)
                                ->required()
                                ->prefixIcon('heroicon-o-cube')
                                ->helperText('Masukkan nama produk maksimal 45 karakter.'),

                            Forms\Components\Select::make('jenis_id')
                                ->label('Jenis Produk')
                                ->relationship('jenis', 'nama') // ubah ke nama agar lebih readable
                                ->required()
                                ->searchable()
                                ->placeholder('Pilih jenis produk')
                                ->preload()
                                ->suffixIcon('heroicon-o-rectangle-stack'),
                        ]),
                    ]),

                Forms\Components\Section::make('Detail Produk')
                    ->description('Tambahkan detail seperti stok, satuan, dan harga.')
                    ->schema([
                        Forms\Components\Grid::make(3)->schema([
                            Forms\Components\TextInput::make('stok')
                                ->label('Stok')
                                ->numeric()
                                ->default(0)
                                ->required()
                                ->prefixIcon('heroicon-o-archive-box'),

                            Forms\Components\Select::make('satuan_id')
                                ->label('Satuan')
                                ->relationship('satuan', 'nama')
                                ->required()
                                ->searchable()
                                ->placeholder('Pilih satuan')
                                ->suffixIcon('heroicon-o-scale'),

                            Forms\Components\TextInput::make('harga')
                                ->label('Harga')
                                ->numeric()
                                ->required()
                                ->prefix('Rp ')
                                ->prefixIcon('heroicon-o-currency-dollar')
                                ->helperText('Masukkan harga dalam rupiah.'),
                        ]),
                    ]),

                Forms\Components\Section::make('Deskripsi Produk')
                    ->description('Berikan deskripsi lengkap tentang produk ini.')
                    ->schema([
                        Forms\Components\Textarea::make('deskripsi')
                            ->label('Deskripsi')
                            ->rows(4)
                            ->placeholder('Tuliskan detail produk...')
                            ->required()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jenis.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('stok')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('satuan.id')
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
            'index' => Pages\ListPupuks::route('/'),
            'create' => Pages\CreatePupuk::route('/create'),
            'view' => Pages\ViewPupuk::route('/{record}'),
            'edit' => Pages\EditPupuk::route('/{record}/edit'),
        ];
    }
}