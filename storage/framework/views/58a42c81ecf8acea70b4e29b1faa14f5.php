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
            <?php $__currentLoopData = $listings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $listing): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($listing->id); ?></td>
                    <td><?php echo e(Str::limit($listing->title, 20)); ?></td>
                    <td><?php echo e(Str::limit($listing->brand ? $listing->brand->name : '', 15)); ?></td>
                    <td><?php echo e(Str::limit($listing->model, 15)); ?></td>
                    <td><?php echo e($listing->year); ?></td>
                    <td><?php echo e($listing->fuel_type); ?></td>
                    <td><?php echo e($listing->transmission); ?></td>
                    <td><?php echo e($listing->km_driven); ?></td>
                    <td><?php echo e($listing->price); ?></td>
                    <td><?php echo e(Str::limit($listing->city, 15)); ?></td>
                    <td><?php echo e($listing->latitude); ?><br><?php echo e($listing->longitude); ?></td>
                    <td><?php echo e($listing->registration_number); ?></td>
                    <td><?php echo e($listing->owners); ?></td>
                    <td><?php echo e(Str::limit($listing->owner_name, 15)); ?><br><?php echo e(Str::limit($listing->owner_email, 15)); ?></td>
                    <td><?php echo e($listing->owner_phone); ?></td>
                    <td><?php echo e($listing->whatsapp_number); ?></td>
                    <td><?php echo e(ucfirst($listing->status)); ?></td>
                    <td><?php echo e($listing->is_active ? 'Y' : 'N'); ?></td>
                    <td><?php echo e($listing->is_featured ? 'Y' : 'N'); ?></td>
                    <td><?php echo e($listing->created_at ? $listing->created_at->format('Y-m-d') : ''); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\sahigadi-ai\resources\views\admin\customer-listings\pdf.blade.php ENDPATH**/ ?>