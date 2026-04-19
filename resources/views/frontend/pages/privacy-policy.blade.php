@extends('layouts.app')

@section('title', 'Privacy Policy - SAHIGADI')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-lg">
                <div class="card-body p-5">
                    <h1 class="fw-bold mb-4">Privacy Policy</h1>
                    <p class="text-muted mb-4">Last Updated: {{ date('F d, Y') }}</p>

                    <hr class="my-4">

                    <h4 class="fw-bold mb-3">1. Introduction</h4>
                    <p class="mb-4">Welcome to SAHIGADI. We are committed to protecting your personal information and your right to privacy. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit our website or use our services.</p>

                    <h4 class="fw-bold mb-3">2. Information We Collect</h4>
                    <p class="mb-2">We collect personal information that you voluntarily provide to us when you:</p>
                    <ul class="mb-4">
                        <li>Register on our platform as a dealer or customer</li>
                        <li>List a car for sale</li>
                        <li>Submit an enquiry about a vehicle</li>
                        <li>Subscribe to our services or newsletters</li>
                        <li>Contact our customer support</li>
                    </ul>
                    <p class="mb-4">The types of information we may collect include your name, email address, phone number, address, vehicle information, and payment details.</p>

                    <h4 class="fw-bold mb-3">3. How We Use Your Information</h4>
                    <p class="mb-2">We use the information we collect or receive to:</p>
                    <ul class="mb-4">
                        <li>Facilitate your account registration and vehicle listings</li>
                        <li>Connect buyers with sellers and dealers</li>
                        <li>Send you administrative information, updates, and marketing communications</li>
                        <li>Improve our services and user experience</li>
                        <li>Comply with legal obligations and enforce our terms</li>
                    </ul>

                    <h4 class="fw-bold mb-3">4. Information Sharing</h4>
                    <p class="mb-4">We do not sell your personal information. We may share your information with:</p>
                    <ul class="mb-4">
                        <li>Service providers who assist in our operations</li>
                        <li>Business partners with your consent</li>
                        <li>Law enforcement agencies when required by law</li>
                    </ul>

                    <h4 class="fw-bold mb-3">5. Data Security</h4>
                    <p class="mb-4">We implement appropriate technical and organizational security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction.</p>

                    <h4 class="fw-bold mb-3">6. Your Rights</h4>
                    <p class="mb-2">You have the right to:</p>
                    <ul class="mb-4">
                        <li>Access and receive a copy of your personal data</li>
                        <li>Request correction of inaccurate data</li>
                        <li>Request deletion of your data</li>
                        <li>Object to processing of your data</li>
                        <li>Request restriction of processing</li>
                    </ul>

                    <h4 class="fw-bold mb-3">7. Cookies</h4>
                    <p class="mb-4">We use cookies and similar tracking technologies to track activity on our website and hold certain information. You can instruct your browser to refuse all cookies or to indicate when a cookie is being sent.</p>

                    <h4 class="fw-bold mb-3">8. Third-Party Links</h4>
                    <p class="mb-4">Our website may contain links to third-party websites. We are not responsible for the privacy practices or content of these external sites.</p>

                    <h4 class="fw-bold mb-3">9. Changes to This Policy</h4>
                    <p class="mb-4">We may update this Privacy Policy from time to time. We will notify you of any changes by posting the new policy on this page and updating the "Last Updated" date.</p>

                    <h4 class="fw-bold mb-3">10. Contact Us</h4>
                    <p class="mb-0">If you have any questions about this Privacy Policy, please contact us at support@sahigadi.com</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection