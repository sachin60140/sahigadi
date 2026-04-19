<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class VehicleSearchesExport implements FromCollection, WithHeadings, WithMapping
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
            'Registration Number',
            'Owner Name',
            'Dealer',
            'Make',
            'Model',
            'Fuel Type',
            'RC Status',
            'Insurance Valid Till',
            'PUC Valid Till',
            'Charge Amount',
            'Status',
            'Search Date',
        ];
    }

    public function map($search): array
    {
        return [
            $search->id,
            $search->registration_number,
            $search->owner_name,
            $search->dealer->name ?? 'N/A',
            $search->make,
            $search->model,
            $search->fuel_type,
            $search->rc_status,
            $search->insurance_date,
            $search->puc_validity,
            number_format($search->charge_amount, 2),
            $search->is_success ? 'Success' : 'Failed',
            $search->created_at->format('d M Y, h:i A'),
        ];
    }
}
