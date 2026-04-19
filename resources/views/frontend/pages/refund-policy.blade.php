@extends('layouts.app')

@section('title', 'Refund Policy - SAHIGADI')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-lg">
                <div class="card-body p-5">
                    <h1 class="fw-bold mb-4">Refund Policy</h1>
                    <p class="text-muted mb-4">Last Updated: {{ date('F d, Y') }}</p>

                    <hr class="my-4">

                    <h4 class="fw-bold mb-3">1. Overview</h4>
                    <p class="mb-4">This Refund Policy outlines the terms and conditions for refunds related to dealer subscriptions, premium listings, and other paid services offered by SAHIGADI. By using our paid services, you agree to this policy.</p>

                    <h4 class="fw-bold mb-3">2. Subscription Plans</h4>
                    <p class="mb-4">SAHIGADI offers various subscription plans for dealers. All subscription payments are processed through secure payment gateways. Subscription fees are non-refundable unless explicitly stated otherwise.</p>

                    <h4 class="fw-bold mb-3">3. Refund Eligibility</h4>
                    <p class="mb-2">You may be eligible for a refund in the following circumstances:</p>
                    <ul class="mb-4">
                        <li><strong>Technical Issues:</strong> If our platform is unavailable for more than 7 consecutive days due to technical issues on our end, you may request a pro-rata refund for the affected period.</li>
                        <li><strong>Duplicate Charges:</strong> If you were charged multiple times for the same transaction, contact us immediately for a full refund of the duplicate charges.</li>
                        <li><strong>Service Not Delivered:</strong> If the paid service was not accessible or functional at the time of purchase, you may request a refund within 7 days of purchase.</li>
                    </ul>

                    <h4 class="fw-bold mb-3">4. Non-Refundable Items</h4>
                    <p class="mb-2">The following are NOT eligible for refunds:</p>
                    <ul class="mb-4">
                        <li>Subscription fees for active periods where services were functional</li>
                        <li>One-time purchases of premium features or add-ons</li>
                        <li>Transaction fees or processing fees</li>
                        <li>Upgrade fees when the upgrade was successfully applied</li>
                        <li>Any fees for services that were used or partially used</li>
                        <li>Requests made after 7 days from the date of purchase</li>
                    </ul>

                    <h4 class="fw-bold mb-3">5. Cancellation</h4>
                    <p class="mb-4">You may cancel your subscription at any time through your account settings. Upon cancellation, your subscription will remain active until the end of the current billing period. No refunds will be provided for the remaining period unless required by applicable law.</p>

                    <h4 class="fw-bold mb-3">6. Wallet Balance</h4>
                    <p class="mb-2">Wallet credits purchased on SAHIGADI are subject to the following:</p>
                    <ul class="mb-4">
                        <li>Wallet credits can be used for vehicle searches, service history reports, and other paid features</li>
                        <li>Wallet credits are non-transferable and non-refundable</li>
                        <li>Unused wallet credits expire after 12 months from the date of purchase</li>
                        <li>No refunds will be provided for unused wallet credits after account closure</li>
                    </ul>

                    <h4 class="fw-bold mb-3">7. How to Request a Refund</h4>
                    <p class="mb-2">To request a refund, please contact us with the following information:</p>
                    <ul class="mb-4">
                        <li>Your registered email address</li>
                        <li>Order/Transaction ID</li>
                        <li>Reason for refund request</li>
                        <li>Any supporting documentation</li>
                    </ul>
                    <p class="mb-4">Email: support@sahigadi.com</p>

                    <h4 class="fw-bold mb-3">8. Refund Processing</h4>
                    <p class="mb-2">Once your refund request is approved:</p>
                    <ul class="mb-4">
                        <li>Refunds will be processed within 7-14 business days</li>
                        <li>Refunds will be credited to the original payment method used for the purchase</li>
                        <li>You will receive email confirmation once the refund is processed</li>
                    </ul>

                    <h4 class="fw-bold mb-3">9. Dispute Resolution</h4>
                    <p class="mb-4">If you are not satisfied with the resolution provided, you may raise a dispute by contacting our customer support. We will make every effort to resolve disputes fairly and promptly.</p>

                    <h4 class="fw-bold mb-3">10. Changes to This Policy</h4>
                    <p class="mb-4">SAHIGADI reserves the right to modify this Refund Policy at any time. Changes will be posted on this page and will take effect immediately upon posting.</p>

                    <h4 class="fw-bold mb-3">11. Contact Us</h4>
                    <p class="mb-0">If you have any questions about this Refund Policy, please contact us at support@sahigadi.com</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection