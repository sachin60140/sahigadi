<?php

namespace App\Services;

use App\Models\Brand;

class SeoService
{
    public function generateListingSeo(array $data): array
    {
        $brand = $data['brand'] ?? null;
        $title = $data['title'] ?? '';
        $year = $data['year'] ?? null;
        $model = $data['model'] ?? '';
        $price = $data['price'] ?? 0;
        $city = $data['city'] ?? 'Patna';
        $kmDriven = $data['km_driven'] ?? null;
        $fuelType = $data['fuel_type'] ?? '';
        $transmission = $data['transmission'] ?? '';
        $brandName = $brand instanceof Brand ? $brand->name : ($brand ?? '');

        $seoTitle = $this->generateTitle($title, $year, $brandName, $model, $city);
        $metaDescription = $this->generateDescription($title, $year, $brandName, $model, $price, $city, $kmDriven, $fuelType, $transmission);
        $metaKeywords = $this->generateKeywords($title, $brandName, $model, $city, $fuelType, $transmission);
        $ogTitle = $this->generateOgTitle($title, $year, $brandName, $price);
        $ogDescription = $this->generateOgDescription($title, $year, $brandName, $kmDriven, $price);

        return [
            'seo_title' => $seoTitle,
            'meta_description' => $metaDescription,
            'meta_keywords' => $metaKeywords,
            'og_title' => $ogTitle,
            'og_description' => $ogDescription,
        ];
    }

    protected function generateTitle(string $title, ?int $year, string $brandName, string $model, string $city): string
    {
        $parts = array_filter([
            $title ?: ($year ? "$year $brandName $model" : "$brandName $model"),
            'for Sale',
            'in '.$city,
            '| SAHIGADI',
        ]);

        return implode(' ', $parts);
    }

    protected function generateDescription(
        string $title,
        ?int $year,
        string $brandName,
        string $model,
        float $price,
        string $city,
        ?int $kmDriven,
        string $fuelType,
        string $transmission
    ): string {
        $description = $title ?: "$year $brandName $model";

        if ($kmDriven) {
            $description .= '. '.number_format($kmDriven).' km driven.';
        }

        if ($fuelType) {
            $description .= ' '.ucfirst($fuelType).' engine.';
        }

        if ($transmission) {
            $description .= ' '.ucfirst($transmission).' transmission.';
        }

        $description .= ' Price: Rs. '.number_format($price).'. Location: '.$city.'. Contact owner/dealer now!';

        return $description;
    }

    protected function generateKeywords(string $title, string $brandName, string $model, string $city, string $fuelType, string $transmission): string
    {
        $keywords = array_filter([
            $title,
            $brandName,
            $model,
            'used car',
            'pre-owned car',
            'second hand car',
            $city,
            $fuelType ? $fuelType.' car' : null,
            $transmission ? $transmission.' car' : null,
            'car for sale',
            'buy used car',
            'used car '.strtolower($city),
        ]);

        return implode(', ', $keywords);
    }

    protected function generateOgTitle(string $title, ?int $year, string $brandName, float $price): string
    {
        return $title.' - '.number_format($price).' Rs | SAHIGADI';
    }

    protected function generateOgDescription(string $title, ?int $year, string $brandName, ?int $kmDriven, float $price): string
    {
        $desc = $title.' - '.($year ?? '').' '.$brandName;

        if ($kmDriven) {
            $desc .= '. '.number_format($kmDriven).' km';
        }

        $desc .= '. Price: Rs. '.number_format($price);

        return $desc;
    }

    public function generateStructuredData(array $data): array
    {
        $brand = $data['brand'] ?? null;
        $title = $data['title'] ?? '';
        $year = $data['year'] ?? null;
        $model = $data['model'] ?? '';
        $price = $data['price'] ?? 0;
        $kmDriven = $data['km_driven'] ?? null;
        $fuelType = $data['fuel_type'] ?? '';
        $transmission = $data['transmission'] ?? '';
        $brandName = $brand instanceof Brand ? $brand->name : ($brand ?? 'Unknown');
        $images = $data['images'] ?? [];
        $sellerName = $data['seller_name'] ?? 'SAHIGADI';
        $sellerType = $data['seller_type'] ?? 'Organization';
        $url = $data['url'] ?? url('/');

        $structuredData = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $title,
            'description' => $this->buildProductDescription($title, $year, $brandName, $model, $kmDriven, $fuelType, $transmission),
            'image' => $images,
            'brand' => [
                '@type' => 'Brand',
                'name' => $brandName,
            ],
            'offers' => [
                '@type' => 'Offer',
                'price' => (string) $price,
                'priceCurrency' => 'INR',
                'availability' => 'https://schema.org/InStock',
                'seller' => [
                    '@type' => $sellerType,
                    'name' => $sellerName,
                ],
            ],
        ];

        if ($kmDriven || $fuelType || $transmission) {
            $structuredData['additionalProperty'] = [];

            if ($kmDriven) {
                $structuredData['additionalProperty'][] = [
                    '@type' => 'PropertyValue',
                    'name' => 'Mileage',
                    'value' => number_format($kmDriven).' km',
                ];
            }

            if ($fuelType) {
                $structuredData['additionalProperty'][] = [
                    '@type' => 'PropertyValue',
                    'name' => 'Fuel Type',
                    'value' => ucfirst($fuelType),
                ];
            }

            if ($transmission) {
                $structuredData['additionalProperty'][] = [
                    '@type' => 'PropertyValue',
                    'name' => 'Transmission',
                    'value' => ucfirst($transmission),
                ];
            }
        }

        return $structuredData;
    }

    protected function buildProductDescription(
        string $title,
        ?int $year,
        string $brandName,
        string $model,
        ?int $kmDriven,
        string $fuelType,
        string $transmission
    ): string {
        $description = "$title - ".($year ? "$year " : '')."$brandName $model.";

        if ($kmDriven) {
            $description .= ' '.number_format($kmDriven).' km driven.';
        }

        if ($fuelType) {
            $description .= ' '.ucfirst($fuelType).' engine.';
        }

        if ($transmission) {
            $description .= ' '.ucfirst($transmission).' transmission.';
        }

        return trim($description);
    }

    public function generateBreadcrumbSchema(array $items): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [],
        ];

        foreach ($items as $index => $item) {
            $schema['itemListElement'][] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $item['name'],
                'item' => $item['url'] ?? null,
            ];
        }

        return $schema;
    }
}
