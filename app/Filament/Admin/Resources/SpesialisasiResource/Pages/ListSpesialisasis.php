<?php

namespace App\Filament\Admin\Resources\SpesialisasiResource\Pages;

use App\Filament\Admin\Resources\SpesialisasiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSpesialisasis extends ListRecords
{
    protected static string $resource = SpesialisasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
