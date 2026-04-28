<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Customer Listings</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 8px; /* Extremely small to fit columns */
        }
        @page {
            size: A4 landscape;
            margin: 10mm;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed; /* Forcing equal-ish widths or preventing massive blowout */
            word-wrap: break-word;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 4px;
            text-align: left;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        th {
            background-color: #f4f4f4;
            font-weight: bold;
        }
        h2 {
            text-align: center;
            margin-bottom: 15px;
            font-size: 16px;
        }
        .col-id { width: 2%; }
        .col-title { width: 6%; }
        .col-brand { width: 5%; }
        .col-model { width: 5%; }
        .col-year { width: 3%; }
        .col-fuel { width: 4%; }
        .col-trans { width: 4%; }
        .col-km { width: 4%; }
        .col-price { width: 5%; }
        .col-city { width: 4%; }
        .col-latlng { width: 5%; }
        .col-reg { width: 5%; }
        .col-owners { width: 3%; }
        .col-owner { width: 6%; }
        .col-phone { width: 5%; }
        .col-wp { width: 5%; }
        .col-status { width: 5%; }
        .col-active { width: 3%; }
        .col-featured { width: 3%; }
        .col-date { width: 6%; }
    </style>
</head>
<body>
    <h2>Customer Car Listings</h2>
    <table>
        <thead>
            <tr>
                <th class="col-id">ID</th>
                <th class="col-title">Title</th>
                <th class="col-brand">Brand</th>
                <th class="col-model">Model</th>
                <th class="col-year">Year</th>
                <th class="col-fuel">Fuel</th>
                <th class="col-trans">Trans</th>
                <th class="col-km">KM</th>
                <th class="col-price">Price</th>
                <th class="col-city">City</th>
                <th class="col-latlng">Lat/Lng</th>
                <th class="col-reg">Reg No</th>
                <th class="col-owners">Owns</th>
                <th class="col-owner">Owner Name / Email</th>
                <th class="col-phone">Phone</th>
                <th class="col-wp">WhatsApp</th>
                <th class="col-status">Status</th>
                <th class="col-active">Actv</th>
                <th class="col-featured">Feat</th>
                <th class="col-date">Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($listings as $listing)
                <tr>
                    <td>{{ $listing->id }}</td>
                    <td>{{ Str::limit($listing->title, 20) }}</td>
                    <td>{{ Str::limit($listing->brand ? $listing->brand->name : '', 15) }}</td>
                    <td>{{ Str::limit($listing->model, 15) }}</td>
                    <td>{{ $listing->year }}</td>
                    <td>{{ $listing->fuel_type }}</td>
                    <td>{{ $listing->transmission }}</td>
                    <td>{{ $listing->km_driven }}</td>
                    <td>{{ $listing->price }}</td>
                    <td>{{ Str::limit($listing->city, 15) }}</td>
                    <td>{{ $listing->latitude }}<br>{{ $listing->longitude }}</td>
                    <td>{{ $listing->registration_number }}</td>
                    <td>{{ $listing->owners }}</td>
                    <td>{{ Str::limit($listing->owner_name, 15) }}<br>{{ Str::limit($listing->owner_email, 15) }}</td>
                    <td>{{ $listing->owner_phone }}</td>
                    <td>{{ $listing->whatsapp_number }}</td>
                    <td>{{ ucfirst($listing->status) }}</td>
                    <td>{{ $listing->is_active ? 'Y' : 'N' }}</td>
                    <td>{{ $listing->is_featured ? 'Y' : 'N' }}</td>
                    <td>{{ $listing->created_at ? $listing->created_at->format('Y-m-d') : '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
