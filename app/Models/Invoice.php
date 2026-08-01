<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'issued_at' => 'datetime',
        'reverse_charge' => 'boolean',
        'taxable_value' => 'decimal:2',
        'cgst_rate' => 'decimal:2',
        'cgst_amount' => 'decimal:2',
        'sgst_rate' => 'decimal:2',
        'sgst_amount' => 'decimal:2',
        'igst_rate' => 'decimal:2',
        'igst_amount' => 'decimal:2',
        'total_tax' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    /** GST state codes as published by the GSTN (first two digits of a GSTIN). */
    public const STATE_CODES = [
        'jammu and kashmir' => '01', 'jammu & kashmir' => '01',
        'himachal pradesh' => '02',
        'punjab' => '03',
        'chandigarh' => '04',
        'uttarakhand' => '05', 'uttaranchal' => '05',
        'haryana' => '06',
        'delhi' => '07', 'new delhi' => '07',
        'rajasthan' => '08',
        'uttar pradesh' => '09',
        'bihar' => '10',
        'sikkim' => '11',
        'arunachal pradesh' => '12',
        'nagaland' => '13',
        'manipur' => '14',
        'mizoram' => '15',
        'tripura' => '16',
        'meghalaya' => '17',
        'assam' => '18',
        'west bengal' => '19',
        'jharkhand' => '20',
        'odisha' => '21', 'orissa' => '21',
        'chhattisgarh' => '22',
        'madhya pradesh' => '23',
        'gujarat' => '24',
        'dadra and nagar haveli and daman and diu' => '26',
        'daman and diu' => '26', 'dadra and nagar haveli' => '26',
        'maharashtra' => '27',
        'karnataka' => '29',
        'goa' => '30',
        'lakshadweep' => '31',
        'kerala' => '32',
        'tamil nadu' => '33',
        'puducherry' => '34', 'pondicherry' => '34',
        'andaman and nicobar islands' => '35', 'andaman and nicobar' => '35',
        'telangana' => '36',
        'andhra pradesh' => '37',
        'ladakh' => '38',
        'other territory' => '97',
    ];

    public function dealer(): BelongsTo
    {
        return $this->belongsTo(Dealer::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /** Resolve a free-text state name to its GST state code, or null if unknown. */
    public static function stateCode(?string $state): ?string
    {
        if (! $state) {
            return null;
        }

        $key = strtolower(trim(preg_replace('/\s+/', ' ', str_replace('&', 'and', $state))));

        return self::STATE_CODES[$key] ?? null;
    }

    /** Indian financial year label (April-March) for a date, e.g. "26-27". */
    public static function financialYear(\DateTimeInterface $date): string
    {
        $year = (int) $date->format('Y');
        $month = (int) $date->format('n');
        $start = $month >= 4 ? $year : $year - 1;

        return sprintf('%02d-%02d', $start % 100, ($start + 1) % 100);
    }

    public function isIntraState(): bool
    {
        return (float) $this->cgst_amount > 0 || (float) $this->sgst_amount > 0;
    }

    /**
     * Amount in words using the Indian numbering system (lakh / crore).
     * Implemented locally because ext-intl is not guaranteed on the host.
     */
    public static function amountInWords(float $amount): string
    {
        $rupees = (int) floor(round($amount, 2));
        $paise = (int) round((round($amount, 2) - $rupees) * 100);

        $words = self::numberToWords($rupees).' Rupees';

        if ($paise > 0) {
            $words .= ' and '.self::numberToWords($paise).' Paise';
        }

        return $words.' Only';
    }

    private static function numberToWords(int $number): string
    {
        if ($number === 0) {
            return 'Zero';
        }

        $ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine',
            'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen',
            'Seventeen', 'Eighteen', 'Nineteen'];
        $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

        $twoDigits = static function (int $n) use ($ones, $tens): string {
            if ($n < 20) {
                return $ones[$n];
            }

            return trim($tens[intdiv($n, 10)].' '.$ones[$n % 10]);
        };

        $threeDigits = static function (int $n) use ($ones, $twoDigits): string {
            $out = '';
            if ($n >= 100) {
                $out = $ones[intdiv($n, 100)].' Hundred';
                $n %= 100;
                if ($n > 0) {
                    $out .= ' ';
                }
            }

            return trim($out.($n > 0 ? $twoDigits($n) : ''));
        };

        $parts = [];

        // Indian grouping: crore, lakh, thousand, then the last three digits.
        foreach ([10000000 => 'Crore', 100000 => 'Lakh', 1000 => 'Thousand'] as $value => $label) {
            if ($number >= $value) {
                $count = intdiv($number, $value);
                $number %= $value;
                // Crore can exceed 999, so recurse for that group.
                $parts[] = ($value === 10000000 ? self::numberToWords($count) : $threeDigits($count)).' '.$label;
            }
        }

        if ($number > 0) {
            $parts[] = $threeDigits($number);
        }

        return implode(' ', $parts);
    }
}
