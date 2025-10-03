<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PengajuanResource\Pages;
use App\Filament\Resources\PengajuanResource\RelationManagers;
use App\Models\Pengajuan;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Get;
use Filament\Forms\Set;

class PengajuanResource extends Resource
{
    protected static ?string $model = Pengajuan::class;
    protected static ?string $navigationGroup = 'Pengajuan & Pupuk';
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $activeNavigationIcon = 'heroicon-s-clipboard-document-check';
    protected static ?int $navigationSort = 1;
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() < 2 ? 'danger' : 'info';
    }
    protected static ?string $navigationBadgeTooltip = 'Total Pengajuan';
    protected static ?string $slug = 'pengajuan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('PengajuanTabs')
                    ->tabs([
                        // === Tab 1: Info Pengajuan ===
                        Tabs\Tab::make('Informasi Pengajuan')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Section::make('Informasi Dasar')
                                    ->description('Lengkapi detail pengajuan sebelum disimpan.')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            Forms\Components\Select::make('pembeli_id')
                                                ->label('Pembeli')
                                                ->relationship('pembeli', 'name')
                                                ->searchable()
                                                ->native(false)
                                                ->preload()
                                                ->required()
                                                ->default(fn() => Auth::id())
                                                ->hidden(fn() => Auth::user()?->hasRole('Pelanggan')) // Pelanggan tidak perlu pilih lagi
                                                ->disabled(fn() => Auth::user()?->hasRole('Administrator') === false) // admin bisa ubah
                                                ->helperText('Pilih pembeli yang mengajukan.')
                                                ->dehydrated()
                                                ->prefixIcon('heroicon-o-user'),

                                            Forms\Components\Select::make('perusahaan_id')
                                                ->label('Perusahaan')
                                                ->relationship('perusahaan', 'nama')
                                                ->searchable()
                                                ->native(false)
                                                ->preload()
                                                ->required()
                                                ->helperText('Pilih perusahaan terkait pengajuan ini.')
                                                ->default(Auth::user()->perusahaan_id)
                                                ->disabled(fn() => Auth::user()?->hasRole('Administrator') === false) // admin bisa ubah
                                                ->dehydrated()
                                                ->prefixIcon('heroicon-o-building-office'),
                                        ]),

                                        Grid::make(2)->schema([
                                            Forms\Components\Select::make('disetujui_oleh')
                                                ->label('Disetujui Oleh')
                                                ->relationship('disetujuiOleh', 'name')
                                                ->default(Auth::user()->hasRole('Administrator') ? Auth::user()->id : null)
                                                ->hidden(fn(Forms\Get $get) => $get('status') !== 'Disetujui')
                                                ->disabled(fn() => Auth::user()?->hasRole('Administrator') === false) // admin bisa ubah
                                                ->dehydratedWhenHidden(),

                                            Forms\Components\DatePicker::make('tanggal_persetujuan')
                                                ->label('Tanggal Persetujuan')
                                                ->placeholder('Pilih Tanggal')
                                                ->default(now())
                                                ->native(false)
                                                ->hidden(fn(Forms\Get $get) => $get('status') !== 'Disetujui')
                                                ->disabled(fn() => Auth::user()?->hasRole('Administrator') === false) // admin bisa ubah
                                                ->dehydratedWhenHidden(),
                                        ]),

                                        Forms\Components\Select::make('status')
                                            ->label('Status')
                                            ->options([
                                                'Menunggu' => 'Menunggu',
                                                'Disetujui' => 'Disetujui',
                                                'Ditolak' => 'Ditolak',
                                            ])
                                            ->default('Menunggu')
                                            ->native(false)
                                            ->required()
                                            ->reactive()
                                            ->prefixIcon('heroicon-o-adjustments-horizontal')
                                            ->hidden(fn() => Auth::user()?->hasRole('Pelanggan')),
                                    ]),
                            ]),

                        // === Tab 2: Detail Pupuk ===
                        Tabs\Tab::make('Detail Pupuk')
                            ->icon('heroicon-o-shopping-cart')
                            ->schema([
                                Section::make('Daftar Pupuk')
                                    ->description('Tambahkan pupuk yang diajukan dalam transaksi ini.')
                                    ->schema([
                                        Forms\Components\Repeater::make('pengajuanDetails')
                                            ->relationship('pengajuanDetails')
                                            ->label('Daftar Pupuk')
                                            ->defaultItems(1)
                                            ->reorderable()
                                            ->collapsible()
                                            ->addActionLabel('Tambah Pupuk')
                                            ->reactive() // penting supaya trigger jalan
                                            ->afterStateUpdated(function (Get $get, Set $set) {
                                                $details = $get('pengajuanDetails') ?? [];
                                                $total = collect($details)->sum(fn($item) => ($item['subtotal'] ?? 0));
                                                $set('grand_total', $total);
                                            })
                                            ->schema([
                                                Grid::make(3)->schema([
                                                    Forms\Components\Select::make('pupuk_id')
                                                        ->label('Pupuk')
                                                        ->relationship('pupuk', 'nama')
                                                        ->searchable()
                                                        ->native(false)
                                                        ->preload()
                                                        ->required()
                                                        ->prefixIcon('heroicon-o-cube'),

                                                    Forms\Components\TextInput::make('jumlah')
                                                        ->label('Jumlah')
                                                        ->numeric()
                                                        ->required()
                                                        ->reactive()
                                                        ->live(onBlur: true)
                                                        ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                                            // Hitung subtotal langsung
                                                            $subtotal = ($state ?? 0) * ($get('harga_satuan') ?? 0);
                                                            $set('subtotal', $subtotal);

                                                            // Update grand_total di induk
                                                            $details = $get('../../pengajuanDetails') ?? [];
                                                            $total = collect($details)->sum(fn($item) => ($item['subtotal'] ?? 0));
                                                            $set('../../grand_total', $total);
                                                        }),

                                                    Forms\Components\TextInput::make('harga_satuan')
                                                        ->label('Harga Satuan')
                                                        ->numeric()
                                                        ->required()
                                                        ->reactive()
                                                        ->live(onBlur: true)
                                                        ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                                            $subtotal = ($get('jumlah') ?? 0) * ($state ?? 0);
                                                            $set('subtotal', $subtotal);

                                                            $details = $get('../../pengajuanDetails') ?? [];
                                                            $total = collect($details)->sum(fn($item) => ($item['subtotal'] ?? 0));
                                                            $set('../../grand_total', $total);
                                                        }),

                                                    Forms\Components\TextInput::make('subtotal')
                                                        ->label('Subtotal')
                                                        ->numeric()
                                                        ->disabled()
                                                        ->dehydrated()
                                                        ->prefixIcon('heroicon-o-calculator'),

                                                    Forms\Components\TextInput::make('total')
                                                        ->label('Total')
                                                        ->numeric()
                                                        ->disabled()
                                                        ->dehydrated()
                                                        ->prefixIcon('heroicon-o-calculator'),
                                                ]),
                                            ]),
                                    ]),
                            ]),

                        // === Tab 3: Transaksi & Pembayaran ===
                        Tabs\Tab::make('Transaksi & Pembayaran')
                            ->icon('heroicon-o-credit-card')
                            ->schema([
                                Section::make('Ringkasan Total')
                                    ->description('Total ini adalah jumlah yang harus dibayar berdasarkan detail pupuk yang diajukan.')
                                    ->schema([
                                        Forms\Components\TextInput::make('grand_total')
                                            ->label('Total Yang Harus Dibayar')
                                            ->numeric()
                                            ->disabled()
                                            ->dehydrated() // kalau anda mau simpan di kolom grand_total tabel pengajuan
                                            ->prefixIcon('heroicon-o-banknotes')
                                            ->afterStateHydrated(function (Forms\Get $get, Forms\Set $set) {
                                                $details = $get('../../pengajuanDetails') ?? [];
                                                $total = collect($details)->sum(fn($item) => ($item['subtotal'] ?? 0));
                                                $set('grand_total', $total);
                                            })
                                            ->reactive()
                                            ->helperText('Total otomatis dihitung dari subtotal tiap pupuk.'),
                                    ]),

                                Section::make('Transaksi')
                                    ->description('Catat transaksi yang terkait dengan pengajuan ini.')
                                    ->schema([
                                        Forms\Components\Repeater::make('transaksis')
                                            ->relationship('transaksis')
                                            ->label('Daftar Transaksi')
                                            ->defaultItems(1)
                                            ->reorderable()
                                            ->collapsible()
                                            ->addActionLabel('Tambah Transaksi')
                                            ->schema([
                                                Forms\Components\Hidden::make('nomor_transaksi')
                                                    ->default(fn() => 'TRX-' . now()->format('YmdHis'))
                                                    ->dehydrated(),

                                                Forms\Components\Hidden::make('tanggal_transaksi')
                                                    ->default(fn() => now())
                                                    ->dehydrated(),

                                                Forms\Components\Hidden::make('total_harga')
                                                    ->dehydrated()
                                                    ->afterStateHydrated(function (Forms\Get $get, Forms\Set $set) {
                                                        $details = $get('../../pengajuanDetails') ?? [];
                                                        $total = collect($details)->sum(fn($item) => ($item['subtotal'] ?? 0));
                                                        $set('total_harga', $total);
                                                    }),

                                                Forms\Components\Hidden::make('perusahaan_id')
                                                    ->default(fn(Forms\Get $get) => $get('../../perusahaan_id'))
                                                    ->dehydrated(),

                                                Forms\Components\Hidden::make('user_id')
                                                    ->default(fn() => Auth::id())
                                                    ->dehydrated(),

                                                // Repeater Pembayaran
                                                Forms\Components\Repeater::make('pembayarans')
                                                    ->relationship('pembayarans')
                                                    ->label('Pembayaran')
                                                    ->defaultItems(1)
                                                    ->collapsible()
                                                    ->addActionLabel('Tambah Pembayaran')
                                                    ->schema([
                                                        Grid::make(2)->schema([
                                                            Forms\Components\Select::make('metode')
                                                                ->label('Metode Pembayaran')
                                                                ->options([
                                                                    'Cash' => 'Cash',
                                                                    'Transfer' => 'Transfer',
                                                                    'Kredit' => 'Kredit',
                                                                ])
                                                                ->required(),

                                                            Forms\Components\TextInput::make('jumlah_bayar')
                                                                ->label('Jumlah Bayar')
                                                                ->numeric()
                                                                ->required(),
                                                        ]),

                                                        Forms\Components\DatePicker::make('tanggal_bayar')
                                                            ->label('Tanggal Bayar')
                                                            ->native(false)
                                                            ->required(),

                                                        Forms\Components\FileUpload::make('bukti')
                                                            ->label('Bukti Pembayaran')
                                                            ->image()
                                                            ->directory('bukti-pembayaran')
                                                            ->downloadable()
                                                            ->openable(),
                                                    ]),
                                            ])
                                            ->hidden(fn() => Auth::user()?->hasRole('Pelanggan')), // Pelanggan tidak bisa ubah transaksi
                                    ]),
                            ]),

                        // === Tab 4: Alasan ===
                        Tabs\Tab::make('Alasan')
                            ->icon('heroicon-o-chat-bubble-bottom-center-text')
                            ->schema([
                                Section::make('Detail Alasan')
                                    ->schema([
                                        Forms\Components\Textarea::make('alasan')
                                            ->label('Alasan Pengajuan')
                                            ->rows(3)
                                            ->required()
                                            ->columnSpanFull(),

                                        Forms\Components\Textarea::make('alasan_penolakan')
                                            ->label('Alasan Penolakan')
                                            ->rows(3)
                                            ->hidden(fn(Forms\Get $get) => $get('status') !== 'Ditolak')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        // === Tab 5: Informasi Sistem ===
                        Tabs\Tab::make('Informasi Sistem')
                            ->icon('heroicon-o-clock')
                            ->schema([
                                Grid::make(2)->schema([
                                    Placeholder::make('created_at')
                                        ->label('Dibuat Pada')
                                        ->content(fn($record) => $record?->created_at?->format('d M Y H:i') ?? '-'),

                                    Placeholder::make('updated_at')
                                        ->label('Diperbarui Pada')
                                        ->content(fn($record) => $record?->updated_at?->format('d M Y H:i') ?? '-'),
                                ]),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString(), // biar tab tidak reset saat reload
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('pembeli.name')
                    ->label('Pembeli')
                    ->icon('heroicon-o-user')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('perusahaan.nama')
                    ->label('Perusahaan')
                    ->icon('heroicon-o-building-office')
                    ->sortable(),

                Tables\Columns\TextColumn::make('disetujuiOleh.name')
                    ->label('Penyetuju')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Menunggu' => 'warning',
                        'Disetujui' => 'success',
                        'Ditolak' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('tanggal_persetujuan')
                    ->label('Tanggal Persetujuan')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diubah')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options([
                        'Menunggu' => 'Menunggu',
                        'Disetujui' => 'Disetujui',
                        'Ditolak' => 'Ditolak',
                    ])
                    ->native(false),

                Tables\Filters\SelectFilter::make('perusahaan_id')
                    ->label('Filter Perusahaan')
                    ->relationship('perusahaan', 'nama')
                    ->searchable()
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\Action::make('cetak_invoice')
                    ->label('Cetak Invoice')
                    ->icon('heroicon-o-printer')
                    ->url(fn($record) => route('pengajuan.invoice.pdf', $record))
                    ->openUrlInNewTab()
                    ->color('primary')
                    ->visible(fn($record) => $record->status === 'Disetujui'),
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
