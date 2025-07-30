<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PengajuanResource\Pages;
use App\Filament\Resources\PengajuanResource\RelationManagers;
use App\Models\Pengajuan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class PengajuanResource extends Resource
{
    protected static ?string $model = Pengajuan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pengajuan')
                    ->description('Lengkapi data pengajuan pupuk dengan benar.')
                    ->schema([
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\Select::make('pembeli_id')
                                ->label('Pembeli')
                                ->relationship('pembeli', 'name')
                                ->placeholder('Pilih Pembeli')
                                ->required()
                                ->searchable()
                                ->preload()
                                ->default(Auth::id())
                                ->prefixIcon('heroicon-o-user'),

                            Forms\Components\Select::make('disetujui_oleh')
                                ->label('Disetujui Oleh')
                                ->placeholder('Pilih Penyetuju')
                                ->relationship('disetujuiOleh', 'name')
                                ->searchable()
                                ->preload()
                                ->prefixIcon('heroicon-o-check-circle'),
                        ]),

                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\DatePicker::make('tanggal_persetujuan')
                                ->label('Tanggal Persetujuan')
                                ->placeholder('Pilih tanggal')
                                ->prefixIcon('heroicon-o-calendar'),

                            Forms\Components\Select::make('status')
                                ->label('Status Pengajuan')
                                ->options([
                                    'Menunggu' => 'Menunggu',
                                    'Disetujui' => 'Disetujui',
                                    'Ditolak' => 'Ditolak',
                                ])
                                ->required()
                                ->default('Menunggu')
                                ->prefixIcon('heroicon-o-adjustments-vertical'),
                        ]),

                        Forms\Components\Textarea::make('alasan_pengajuan')
                            ->label('Alasan Pengajuan')
                            ->placeholder('Masukkan alasan pengajuan')
                            ->rows(3)
                            ->autosize()
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('alasan_penolakan')
                            ->label('Alasan Penolakan')
                            ->placeholder('Isi jika status ditolak...')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('Detail Pengajuan')
                    ->description('Tambahkan daftar pupuk yang diajukan beserta jumlah dan harga.')
                    ->schema([
                        Forms\Components\Repeater::make('pengajuanDetails')
                            ->relationship() // pastikan relasi di model sudah benar (hasMany)
                            ->schema([
                                Forms\Components\Grid::make(3)->schema([
                                    Forms\Components\Select::make('pupuk_id')
                                        ->label('Pupuk')
                                        ->relationship('pupuk', 'nama') // tampilkan nama pupuk, bukan ID
                                        ->required()
                                        ->searchable()
                                        ->placeholder('Pilih Pupuk')
                                        ->preload(),

                                    Forms\Components\TextInput::make('jumlah')
                                        ->label('Jumlah')
                                        ->numeric()
                                        ->required()
                                        ->prefixIcon('heroicon-o-hashtag'),

                                    Forms\Components\TextInput::make('total')
                                        ->label('Total Harga')
                                        ->numeric()
                                        ->required()
                                        ->prefix('Rp ')
                                        ->prefixIcon('heroicon-o-currency-dollar'),
                                ]),
                            ])
                            ->columns(1)
                            ->addActionLabel('Tambah Pupuk')
                            ->collapsible(),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('pembeli.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('disetujui_oleh')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_persetujuan')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
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
            'index' => Pages\ListPengajuans::route('/'),
            'create' => Pages\CreatePengajuan::route('/create'),
            'view' => Pages\ViewPengajuan::route('/{record}'),
            'edit' => Pages\EditPengajuan::route('/{record}/edit'),
        ];
    }
}