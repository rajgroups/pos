<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faqs = [
            [
                'category' => 'Rides & Bookings',
                'question' => 'How do I cancel my ride?',
                'answer' => 'You can cancel your ride from the active trip screen or finding driver screen by tapping the Cancel button. Cancellation fees may apply if the driver has already arrived at your location.',
                'sequence' => 1,
            ],
            [
                'category' => 'Rides & Bookings',
                'question' => 'What happens if I lose an item in the cab?',
                'answer' => 'Go to your Ride History, select the specific ride, and tap "Report Lost Item" or submit a support ticket to connect directly with our support team and driver.',
                'sequence' => 2,
            ],
            [
                'category' => 'Payments & Refunds',
                'question' => 'How do I apply a promo code?',
                'answer' => 'Before confirming your booking, tap on "Offers & Coupons" on the ride estimate screen to apply available promo codes and discounts.',
                'sequence' => 3,
            ],
            [
                'category' => 'Payments & Refunds',
                'question' => 'What payment methods are supported?',
                'answer' => 'We accept Cash, UPI (GPay, PhonePe, Paytm), Credit/Debit Cards, and Indicab Wallet balance.',
                'sequence' => 4,
            ],
            [
                'category' => 'Safety & Emergency',
                'question' => 'How does the SOS emergency feature work?',
                'answer' => 'During any active trip, tap the SOS button to instantly alert local emergency contacts, share your live GPS location, and trigger support team assistance.',
                'sequence' => 5,
            ],
            [
                'category' => 'Account & Profile',
                'question' => 'How can I update my profile details?',
                'answer' => 'Open the Profile screen from the side menu, tap on your avatar or Edit Profile, and update your name, email, address, or emergency contacts.',
                'sequence' => 6,
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::updateOrCreate(
                ['question' => $faq['question']],
                [
                    'category' => $faq['category'],
                    'answer' => $faq['answer'],
                    'sequence' => $faq['sequence'],
                    'is_active' => true,
                ]
            );
        }
    }
}
