<?php

namespace App\Services;

use App\Models\Currency;

class CurrencyAmountFormatter
{
    /**
     * Format amount for all currencies, including Naira (₦) as main currency and extra currencies.
     *
     * @param  object  $training
     * @param  string  $type
     * @param  float|null  $amount
     * @return array
     */
    public function format($training, $type = null, $amount = null): array
    {
        $string = '';
        $array = [];

        $amountToUse = $amount ?? $training->p_amount;

        $customAmounts = [];

        // Normalize currencies into array of [id => amount]
        if (!empty($training->currencies)) {
            foreach ($training->currencies as $currency) {
                $currencyId = is_object($currency) ? $currency->id : $currency['id'];
                $currencyAmount = is_object($currency) ? $currency->amount ?? null : $currency['amount'] ?? null;

                $customAmounts[(int) $currencyId] = $currencyAmount;
            }
        }

        // Directly use Naira (₦) for the main currency, based on training's p_amount
        $nairaSymbol = '₦';
        $mainAmount = ($type === 'part') ? $amountToUse / 2 : $amountToUse;

        $string .= '<strong>' . $nairaSymbol . '</strong>' . number_format($mainAmount, 0);
        $array['main'] = [
            'symbol' => $nairaSymbol,
            'name' => 'Naira',
            'amount' => number_format($mainAmount, 0),
            'raw_amount' => $mainAmount
        ];

        // Now process extra currencies
        if (!empty($customAmounts)) {
            $currencyIds = array_keys($customAmounts);

            $currencies = Currency::where('status', 1)
                ->whereIn('id', $currencyIds)
                ->get();
            
            foreach ($currencies as $cur) {
                $id = $cur->id;

                if (isset($customAmounts[$id]) && $customAmounts[$id] !== null) {
                    $finalAmount = $customAmounts[$id];
                } else {
                    $converted = $cur->conversion_rate * $amountToUse;
                    $finalAmount = $converted;
                }

                // Apply "part" rule after getting final amount
                if ($type === 'part') {
                    $finalAmount /= 2;
                }

                $string .= ' <span style="color:black">|</span> <strong>'
                    . $cur->symbol . '</strong>' . number_format($finalAmount, 0);

                $key = $cur->country_name ?: ($cur->code ?? $cur->id);

                $array[$key] = [
                    'symbol' => $cur->symbol,
                    'name' => $cur->name,
                    'amount' => number_format($finalAmount, 0),
                    'raw_amount' => $finalAmount
                ];
            }
        }

        return [
            'string' => $string,
            'array' => $array
        ];
    }

    /**
     * Get price range for all sub-programs, formatted with currencies.
     *
     * @param  object  $training
     * @param  string  $type
     * @return array
     */
    public function getPriceRangeWithCurrencies($training, $type = null): array
    {
        // Get all amounts including the base (main) program and sub-programs
        $amounts = collect([$training->p_amount]);

        // Add sub-programs' amounts
        if ($training->subPrograms->isNotEmpty()) {
            $amounts = $amounts->merge($training->subPrograms->pluck('p_amount'));
        }

        $from = $amounts->min();
        $to = $amounts->max();
        
        $minFormatted = $this->format($training, $type, $from);
        $maxFormatted = $this->format($training, $type, $to);

        return [
            'from' => [
                'formatted' => $minFormatted['string'],
                'raw' => $minFormatted['array']
            ],
            'to' => [
                'formatted' => $maxFormatted['string'],
                'raw' => $maxFormatted['array']
            ]
        ];
    }
}
