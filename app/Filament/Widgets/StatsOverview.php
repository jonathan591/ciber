<?php

namespace App\Filament\Widgets;

use App\Models\Audit;
use App\Models\IntellectualProperty;
use App\Models\User;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Usuario', User::count())

                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),
            Stat::make('Propiedad Intelectual', IntellectualProperty::count())
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([17, 2, 10, 3, 15, 4, 3])
                ->color('info'),
            Stat::make('Auditoria', Audit::count())
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('danger'),
        ];
    }
}