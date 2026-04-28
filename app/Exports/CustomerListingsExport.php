<?php

namespace App\Exports;

use App\Models\CustomerCarListing;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustomerListingsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function collection()
    {
        return $this->query->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Title',
            'Slug',
            'Brand',
            'Model',
            'Year',
            'Fuel Type',
            'Transmission',
            'KM Driven',
            'Price',
            'Description',
            'City',
            'Latitude',
            'Longitude',
            'Registration Number',
            'Owners',
            'Owner Name',
            'Owner Email',
            'Owner Phone',
            'WhatsApp Number',
            'Status',
            'Rejection Reason',
            'Images (JSON)',
            'Is Active',
            'Is Featured',
            'Featured Expires At',
            'Created At',
            'Updated At',
            'Deleted At'
        ];
    }

    public function map($listing): array
    {
        return [
            $listing->id,
            $listing->title,
            $listing->slug,
            $listing->brand ? $listing->brand->name : 'N/A',
            $listing->model,
            $listing->year,
            $listing->fuel_type,
            $listing->transmission,
            $listing->km_driven,
            $listing->price,
            $listing->description,
            $listing->city,
            $listing->latitude,
            $listing->longitude,
            $listing->registration_number,
            $listing->owners,
            $listing->owner_name,
            $listing->owner_email,
            $listing->owner_phone,
            $listing->whatsapp_number,
            ucfirst($listing->status),
            $listing->rejection_reason,
            $listing->images,
            $listing->is_active ? 'Yes' : 'No',
            $listing->is_featured ? 'Yes' : 'No',
            $listing->featured_expires_at,
            $listing->created_at ? $listing->created_at->format('Y-m-d H:i:s') : null,
            $listing->updated_at ? $listing->updated_at->format('Y-m-d H:i:s') : null,
            $listing->deleted_at ? $listing->deleted_at->format('Y-m-d H:i:s') : null,
        ];
    }
}
