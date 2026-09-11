<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <title>Risiti - <?php echo e($order->order_number); ?></title>
    <style>
        body {
            font-family: 'DejaVu Sans', 'Helvetica', Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            font-size: 12px;
        }
        .container {
            max-width: 100%;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #FF6B35;
            padding-bottom: 15px;
        }
        .header h1 {
            color: #FF6B35;
            margin: 0 0 5px 0;
            font-size: 24px;
        }
        .header p {
            margin: 0;
            color: #666;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .info-box {
            width: 48%;
        }
        .info-box h4 {
            margin: 0 0 8px 0;
            color: #FF6B35;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .info-box p {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            color: #333;
            font-weight: bold;
        }
        .text-end {
            text-align: right;
        }
        .fw-bold {
            font-weight: bold;
        }
        .total-row {
            background-color: #f8f9fa;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #666;
            font-size: 11px;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
        }
        .badge-success {
            background-color: #d4edda;
            color: #155724;
        }
        .badge-warning {
            background-color: #fff3cd;
            color: #856404;
        }
        .badge-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
        .badge-secondary {
            background-color: #e2e3e5;
            color: #383d41;
        }
    </style>
</head>
<body>
    <div class="container">
        
        <div class="header">
            <h1><span style="color: #FF6B35;">🔥</span> GasPOA Market</h1>
            <p>Risiti Rasmi ya Agizo</p>
        </div>

        
        <div class="info-row">
            <div class="info-box">
                <h4>Maelezo ya Agizo</h4>
                <p><strong>Namba:</strong> <?php echo e($order->order_number); ?></p>
                <p><strong>Tarehe:</strong> <?php echo e($order->created_at ? $order->created_at->format('d M Y, H:i') : 'Hivi karibuni'); ?></p>
                <p>
                    <strong>Hali:</strong> 
                    <?php
                        $statusLabels = [
                            'pending' => 'Inasubiri',
                            'accepted' => 'Imekubaliwa',
                            'picked_up' => 'Imeshachukuliwa',
                            'out_for_delivery' => 'Njiani',
                            'delivered' => 'Imekamilika',
                            'cancelled' => 'Imefutwa',
                        ];
                        $statusLabel = $statusLabels[$order->status] ?? ucfirst(str_replace('_', ' ', $order->status));
                    ?>
                    <?php echo e($statusLabel); ?>

                </p>
                <p>
                    <strong>Kasi:</strong> 
                    <?php echo e(($order->urgency_level ?? 'normal') === 'urgent' ? 'Haraka' : 'Kawaida'); ?>

                </p>
            </div>
            <div class="info-box">
                <h4>Maelezo ya Mteja</h4>
                <p><strong>Jina:</strong> <?php echo e($order->consumer->full_name ?? 'Mteja'); ?></p>
                <p><strong>Simu:</strong> <?php echo e($order->consumer->phone_number ?? '-'); ?></p>
                <p><strong>Barua Pepe:</strong> <?php echo e($order->consumer->email ?? '-'); ?></p>
            </div>
        </div>

        
        <div class="info-row">
            <div class="info-box">
                <h4>Anwani ya Kufikishia</h4>
                <p><?php echo e($order->delivery_address); ?></p>
            </div>
            <div class="info-box">
                <h4>Muuzaji</h4>
                <p><strong><?php echo e($order->retailer->business_name ?? 'Haijulikani'); ?></strong></p>
                <p><?php echo e($order->retailer->physical_address ?? ''); ?></p>
                <p>Simu: <?php echo e($order->retailer->user->phone_number ?? '-'); ?></p>
            </div>
        </div>

        
        <table>
            <thead>
                <tr>
                    <th>Bidhaa</th>
                    <th>Idadi</th>
                    <th>Bei/Unit (TZS)</th>
                    <th>Jumla (TZS)</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($item->product->name ?? 'Bidhaa'); ?> (<?php echo e($item->product->weight_kg ?? 0); ?>kg)</td>
                    <td><?php echo e($item->quantity); ?></td>
                    <td><?php echo e(number_format($item->price_per_item)); ?></td>
                    <td><?php echo e(number_format($item->price_per_item * $item->quantity)); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
            <tfoot>
                <?php
                    $subtotal = $order->items->sum(function($item) {
                        return $item->price_per_item * $item->quantity;
                    });
                    $isUrgent = ($order->urgency_level ?? 'normal') === 'urgent';
                    $urgencyFee = $isUrgent ? 3000 : 0;
                ?>
                <tr>
                    <td colspan="3" class="text-end"><strong>Jumla ya Bidhaa:</strong></td>
                    <td>TZS <?php echo e(number_format($subtotal)); ?></td>
                </tr>
                <tr>
                    <td colspan="3" class="text-end"><strong>Usafirishaji:</strong></td>
                    <td>TZS 0 (Bure)</td>
                </tr>
                <?php if($isUrgent): ?>
                <tr>
                    <td colspan="3" class="text-end"><strong>Ada ya Haraka:</strong></td>
                    <td>TZS <?php echo e(number_format($urgencyFee)); ?></td>
                </tr>
                <?php endif; ?>
                <tr class="total-row">
                    <td colspan="3" class="text-end fw-bold"><strong>Jumla Kuu:</strong></td>
                    <td class="fw-bold"><strong>TZS <?php echo e(number_format($order->total_amount)); ?></strong></td>
                </tr>
            </tfoot>
        </table>

        
        <div class="info-row">
            <div class="info-box">
                <h4>Maelezo ya Malipo</h4>
                <?php
                    $paymentMethodLabels = [
                        'cash' => 'Pesa Taslimu',
                        'mobile_money' => 'M-Pesa/TigoPesa/Airtel Money',
                        'card' => 'Kadi ya Benki',
                    ];
                    $paymentMethod = $paymentMethodLabels[$order->payment_method] ?? ucfirst($order->payment_method);
                ?>
                <p><strong>Njia ya Malipo:</strong> <?php echo e($paymentMethod); ?></p>
                <p>
                    <strong>Hali ya Malipo:</strong> 
                    <?php if($order->payment_status == 'paid'): ?>
                        <span style="color: #155724;">Imelipwa</span>
                    <?php elseif($order->payment_status == 'failed'): ?>
                        <span style="color: #721c24;">Imeshindikana</span>
                    <?php else: ?>
                        <span style="color: #856404;">Haijalipwa</span>
                    <?php endif; ?>
                </p>
                <?php if($order->transaction_reference): ?>
                <p><strong>Kumbukumbu:</strong> <?php echo e($order->transaction_reference); ?></p>
                <?php endif; ?>
            </div>
        </div>

        
        <div class="footer">
            <p>Asante kwa kutumia GasPOA Market!</p>
            <p>Kwa msaada zaidi, piga *150*99# au wasiliana nasi kupitia info@gesilink.co.tz</p>
            <p>Risiti ilitolewa: <?php echo e(now()->format('d M Y, H:i')); ?></p>
        </div>
    </div>
</body>
</html><?php /**PATH C:\xampp\htdocs\GasPOA\resources\views\consumer\order\receipt-pdf.blade.php ENDPATH**/ ?>