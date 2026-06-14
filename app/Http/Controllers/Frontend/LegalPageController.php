<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class LegalPageController extends Controller
{
    public function privacy()
    {
        return $this->renderPage(
            'Privacy Policy',
            'Read the SAHI GADI privacy policy to understand how customer, dealer, vehicle listing and payment information is collected, used and protected.',
            route('privacy-policy'),
            [
                ['title' => 'Introduction', 'paragraphs' => ['Welcome to SAHI GADI. We are committed to protecting your personal information and your right to privacy. This policy explains how we collect, use, disclose and safeguard information when you visit our website or use our services.']],
                ['title' => 'Information We Collect', 'paragraphs' => ['We collect personal information that you voluntarily provide when you use the platform.'], 'items' => ['Register as a dealer or customer', 'List a car for sale', 'Submit an enquiry about a vehicle', 'Subscribe to our services or communications', 'Contact customer support'], 'footer' => 'Information may include your name, email address, phone number, address, vehicle information and payment-related details.'],
                ['title' => 'How We Use Your Information', 'items' => ['Facilitate account registration and vehicle listings', 'Connect buyers with sellers and dealers', 'Send administrative information, updates and permitted marketing communications', 'Improve our services and user experience', 'Comply with legal obligations and enforce our terms']],
                ['title' => 'Information Sharing', 'paragraphs' => ['We do not sell your personal information. We may share information only where needed to operate the service, with your consent or when required by law.'], 'items' => ['Service providers who assist our operations', 'Business partners where you have provided consent', 'Law enforcement or regulators when legally required']],
                ['title' => 'Data Security', 'paragraphs' => ['We use appropriate technical and organizational safeguards designed to protect personal information against unauthorized access, alteration, disclosure or destruction.']],
                ['title' => 'Your Rights', 'items' => ['Access and receive a copy of your personal data', 'Request correction of inaccurate data', 'Request deletion where legally available', 'Object to or restrict certain processing']],
                ['title' => 'Cookies', 'paragraphs' => ['We use cookies and similar technologies to operate the website, remember preferences and understand usage. Browser settings can be used to refuse or manage cookies.']],
                ['title' => 'Third-Party Links', 'paragraphs' => ['Our website may link to third-party websites. Their privacy practices and content are governed by their own policies.']],
                ['title' => 'Changes to This Policy', 'paragraphs' => ['We may update this policy from time to time. Material changes will be posted on this page with a revised update date.']],
                ['title' => 'Contact Us', 'paragraphs' => ['Questions about this Privacy Policy can be sent to support@sahigadi.com.']],
            ]
        );
    }

    public function terms()
    {
        return $this->renderPage(
            'Terms of Use',
            'Read the SAHI GADI terms of use for customers, dealers, used car listings, enquiries, subscriptions and paid verification services.',
            route('terms-of-use'),
            [
                ['title' => 'Acceptance of Terms', 'paragraphs' => ['By accessing or using SAHI GADI, you agree to these terms. If you do not agree, please do not use the platform or its services.']],
                ['title' => 'Description of Service', 'paragraphs' => ['SAHI GADI is an online marketplace connecting buyers with sellers and dealers of pre-owned vehicles. We support vehicle listings, browsing, enquiries and related verification services.']],
                ['title' => 'User Registration and Account', 'items' => ['Provide accurate and complete registration information', 'Protect your account credentials', 'Accept responsibility for activity under your account', 'Notify us promptly about unauthorized account use']],
                ['title' => 'Dealer Responsibilities', 'items' => ['Provide accurate vehicle information', 'Ensure listings comply with applicable laws', 'Respond to genuine customer enquiries', 'Maintain valid business credentials and licences', 'Honor commitments made through the platform']],
                ['title' => 'Prohibited Activities', 'items' => ['Post false, misleading or fraudulent information', 'Use the platform for an illegal purpose', 'Harass, abuse or harm other users', 'Transmit malware or harmful code', 'Attempt unauthorized access', 'Interfere with platform availability or operation']],
                ['title' => 'Content and Intellectual Property', 'paragraphs' => ['Platform content, branding, graphics and software belong to SAHI GADI or their respective rights holders. They may not be reproduced, distributed or modified without permission.']],
                ['title' => 'Vehicle Listing Rules', 'items' => ['List only vehicles that exist and are available for sale', 'Use accurate descriptions, specifications and pricing', 'Provide clear representative photographs', 'Disclose known defects or material issues', 'Comply with applicable motor-vehicle regulations']],
                ['title' => 'Disclaimer of Warranties', 'paragraphs' => ['The platform is provided on an "as is" and "as available" basis. We do not guarantee the accuracy, completeness or reliability of user-submitted listings or content.']],
                ['title' => 'Limitation of Liability', 'paragraphs' => ['To the extent permitted by law, SAHI GADI is not liable for indirect, incidental, special, consequential or punitive damages arising from use of the platform.']],
                ['title' => 'Indemnification', 'paragraphs' => ['You agree to indemnify and hold SAHI GADI and its team harmless from claims or losses arising from your misuse of the platform or violation of these terms.']],
                ['title' => 'Termination', 'paragraphs' => ['We may suspend or terminate access where an account violates these terms, creates risk for other users or is used unlawfully.']],
                ['title' => 'Governing Law', 'paragraphs' => ['These terms are governed by the laws of India. Applicable disputes will be handled by courts with appropriate jurisdiction.']],
                ['title' => 'Changes to Terms', 'paragraphs' => ['We may update these terms. Continued use after an update means you accept the revised terms.']],
                ['title' => 'Contact Information', 'paragraphs' => ['Questions about these Terms of Use can be sent to support@sahigadi.com.']],
            ]
        );
    }

    public function refunds()
    {
        return $this->renderPage(
            'Refund Policy',
            'Read the SAHI GADI refund policy for dealer subscriptions, premium listings, payment services and vehicle verification reports.',
            route('refund-policy'),
            [
                ['title' => 'Overview', 'paragraphs' => ['This policy explains refund conditions for dealer subscriptions, premium listings and other paid SAHI GADI services. By purchasing a paid service, you agree to this policy.']],
                ['title' => 'Subscription Plans', 'paragraphs' => ['Dealer subscription payments are processed through secure gateways. Subscription fees are non-refundable unless this policy or applicable law expressly provides otherwise.']],
                ['title' => 'Refund Eligibility', 'items' => ['Technical issues: a pro-rata refund may be considered when our platform is unavailable for more than seven consecutive days due to an issue on our side', 'Duplicate charges: duplicate payment amounts will be reviewed and eligible duplicates refunded', 'Service not delivered: requests may be considered within seven days when a paid service was not accessible or functional at purchase']],
                ['title' => 'Non-Refundable Items', 'items' => ['Active subscription periods where services were functional', 'Successfully delivered premium features or add-ons', 'Gateway or transaction processing fees where non-refundable', 'Successfully applied upgrades', 'Services already used or partially used', 'Requests made more than seven days after purchase unless required by law']],
                ['title' => 'Cancellation', 'paragraphs' => ['Subscriptions may be cancelled according to the options available in your account. Access normally remains active through the paid billing period, and unused time is not refunded unless required by law.']],
                ['title' => 'Wallet Balance', 'items' => ['Wallet credits may be used for eligible reports and paid features', 'Wallet credits are non-transferable and ordinarily non-refundable', 'Unused credits may expire after 12 months from purchase', 'Account closure does not automatically create a refund entitlement']],
                ['title' => 'How to Request a Refund', 'paragraphs' => ['Email support@sahigadi.com with the details needed to review your request.'], 'items' => ['Registered email address or phone number', 'Order or transaction ID', 'Reason for the request', 'Relevant supporting documents']],
                ['title' => 'Refund Processing', 'items' => ['Approved refunds are normally initiated within 7-14 business days', 'Refunds are sent to the original payment method where possible', 'A confirmation is sent after processing']],
                ['title' => 'Dispute Resolution', 'paragraphs' => ['If you are dissatisfied with a decision, contact customer support with the relevant transaction details. We will review the dispute fairly and promptly.']],
                ['title' => 'Changes to This Policy', 'paragraphs' => ['We may update this Refund Policy. Revisions will be posted here and apply from their stated effective date.']],
                ['title' => 'Contact Us', 'paragraphs' => ['Questions about this Refund Policy can be sent to support@sahigadi.com.']],
            ]
        );
    }

    private function renderPage(string $title, string $description, string $canonical, array $sections)
    {
        return Inertia::render('Public/LegalPage', [
            'title' => $title,
            'description' => $description,
            'canonical' => $canonical,
            'lastUpdated' => 'June 13, 2026',
            'sections' => collect($sections)->values()->map(
                fn (array $section, int $index) => [
                    'number' => $index + 1,
                    'title' => $section['title'],
                    'paragraphs' => $section['paragraphs'] ?? [],
                    'items' => $section['items'] ?? [],
                    'footer' => $section['footer'] ?? null,
                ]
            ),
        ]);
    }
}
