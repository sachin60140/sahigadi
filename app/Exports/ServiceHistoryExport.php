<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ServiceHistoryExport implements FromCollection, WithHeadings, WithMapping
{
    protected $searches;

    public function __construct($searches)
    {
        $this->searches = $searches;
    }

    public function collection()
    {
        return $this->searches;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Vehicle Number',
            'Dealer',
            'Service Count',
            'Charge Amount',
            'Status',
            'Search Date',
        ];
    }

    public function map($search): array
    {
        return [
            $search->id,
            $search->vehicle_number,
            $search->dealer->name ?? 'N/A',
            $search->service_count,
            number_format($search->charge_amount, 2),
            $search->is_success ? 'Success' : 'Failed',
            $search->created_at->format('d M Y, h:i A'),
        ];
    }
}
