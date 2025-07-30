<?php

namespace App\Filament\Resources\PengajuanDetailResource\Pages;

use App\Filament\Resources\PengajuanDetailResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPengajuanDetail extends ViewRecord
{
    protected static string $resource = PengajuanDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
