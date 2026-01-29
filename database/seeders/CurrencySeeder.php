<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\GeneralResourceService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run():void
    {
        \DB::table('currencies')->truncate();

        // $table->string('name');
        // $table->string('conversion_rate')->default(1);
        // $table->string('symbol')->nullable();
        // $table->string('symbol_native')->nullable();
        // $table->tinyInteger('status')->default(0);

        $insert =
        [
            "USD" => [
                "name" => "US Dollar",
                "conversion_rate" => 1,
                "symbol" => "$",
                "symbol_native" => "$",
                "status" => 0
            ],
            "CAD" => [
                "name" => "Canadian Dollar",
                "conversion_rate" => 1,
                "symbol" => "CA$",
                "symbol_native" => "$",
                "status" => 0
            ],
            "EUR" => [
                "name" => "Euro",
                "conversion_rate" => 1,
                "symbol" => "\u20ac",
                "symbol_native" => "\u20ac",
                "status" => 0
            ],
            "AED" => [
                "name" => "United Arab Emirates Dirham",
                "conversion_rate" => 1,
                "symbol" => "AED",
                "symbol_native" => "\u062f.\u0625.\u200f",
                "status" => 0
            ],
            "AFN" => [
                "name" => "Afghan Afghani",
                "conversion_rate" => 1,
                "symbol" => "Af",
                "symbol_native" => "\u060b",
                "status" => 0
            ],
            "ALL" => [
                "name" => "Albanian Lek",
                "conversion_rate" => 1,
                "symbol" => "ALL",
                "symbol_native" => "Lek",
                "status" => 0
            ],
            "AMD" => [
                "name" => "Armenian Dram",
                "conversion_rate" => 1,
                "symbol" => "AMD",
                "symbol_native" => "\u0564\u0580.",
                "status" => 0
            ],
            "ARS" => [
                "name" => "Argentine Peso",
                "conversion_rate" => 1,
                "symbol" => "AR$",
                "symbol_native" => "$",
                "status" => 0
            ],
            "AUD" => [
                "name" => "Australian Dollar",
                "conversion_rate" => 1,
                "symbol" => "AU$",
                "symbol_native" => "$",
                "status" => 0
            ],
            "AZN" => [
                "name" => "Azerbaijani Manat",
                "conversion_rate" => 1,
                "symbol" => "man.",
                "symbol_native" => "\u043c\u0430\u043d.",
                "status" => 0
            ],
            "BAM" => [
                "name" => "Bosnia-Herzegovina Convertible Mark",
                "conversion_rate" => 1,
                "symbol" => "KM",
                "symbol_native" => "KM",
                "status" => 0
            ],
            "BDT" => [
                "name" => "Bangladeshi Taka",
                "conversion_rate" => 1,
                "symbol" => "Tk",
                "symbol_native" => "\u09f3",
                "status" => 0
            ],
            "BGN" => [
                "name" => "Bulgarian Lev",
                "conversion_rate" => 1,
                "symbol" => "BGN",
                "symbol_native" => "\u043b\u0432.",
                "status" => 0
            ],
            "BHD" => [
                "name" => "Bahraini Dinar",
                "conversion_rate" => 1,
                "symbol" => "BD",
                "symbol_native" => "\u062f.\u0628.\u200f",
                "status" => 0
            ],
            "BIF" => [
                "name" => "Burundian Franc",
                "conversion_rate" => 1,
                "symbol" => "FBu",
                "symbol_native" => "FBu",
                "status" => 0
            ],
            "BND" => [
                "name" => "Brunei Dollar",
                "conversion_rate" => 1,
                "symbol" => "BN$",
                "symbol_native" => "$",
                "status" => 0
            ],
            "BOB" => [
                "name" => "Bolivian Boliviano",
                "conversion_rate" => 1,
                "symbol" => "Bs",
                "symbol_native" => "Bs",
                "status" => 0
            ],
            "BRL" => [
                "name" => "Brazilian Real",
                "conversion_rate" => 1,
                "symbol" => "R$",
                "symbol_native" => "R$",
                "status" => 0
            ],
            "BWP" => [
                "name" => "Botswanan Pula",
                "conversion_rate" => 1,
                "symbol" => "P",
                "symbol_native" => "P",
                "status" => 0
            ],
            "BYN" => [
                "name" => "Belarusian Ruble",
                "conversion_rate" => 1,
                "symbol" => "Br",
                "symbol_native" => "\u0440\u0443\u0431.",
                "status" => 0
            ],
            "BZD" => [
                "name" => "Belize Dollar",
                "conversion_rate" => 1,
                "symbol" => "BZ$",
                "symbol_native" => "$",
                "status" => 0
            ],
            "CDF" => [
                "name" => "Congolese Franc",
                "conversion_rate" => 1,
                "symbol" => "CDF",
                "symbol_native" => "FrCD",
                "status" => 0
            ],
            "CHF" => [
                "name" => "Swiss Franc",
                "conversion_rate" => 1,
                "symbol" => "CHF",
                "symbol_native" => "CHF",
                "status" => 0
            ],
            "CLP" => [
                "name" => "Chilean Peso",
                "conversion_rate" => 1,
                "symbol" => "CL$",
                "symbol_native" => "$",
                "status" => 0
            ],
            "CNY" => [
                "name" => "Chinese Yuan",
                "conversion_rate" => 1,
                "symbol" => "CN\u00a5",
                "symbol_native" => "CN\u00a5",
                "status" => 0
            ],
            "COP" => [
                "name" => "Colombian Peso",
                "conversion_rate" => 1,
                "symbol" => "CO$",
                "symbol_native" => "$",
                "status" => 0
            ],
            "CRC" => [
                "name" => "Costa Rican Col\u00f3n",
                "conversion_rate" => 1,
                "symbol" => "\u20a1",
                "symbol_native" => "\u20a1",
                "status" => 0
            ],
            "CVE" => [
                "name" => "Cape Verdean Escudo",
                "conversion_rate" => 1,
                "symbol" => "CV$",
                "symbol_native" => "CV$",
                "status" => 0
            ],
            "CZK" => [
                "name" => "Czech Republic Koruna",
                "conversion_rate" => 1,
                "symbol" => "K\u010d",
                "symbol_native" => "K\u010d",
                "status" => 0
            ],
            "DJF" => [
                "name" => "Djiboutian Franc",
                "conversion_rate" => 1,
                "symbol" => "Fdj",
                "symbol_native" => "Fdj",
                "status" => 0
            ],
            "DKK" => [
                "name" => "Danish Krone",
                "conversion_rate" => 1,
                "symbol" => "Dkr",
                "symbol_native" => "kr",
                "status" => 0
            ],
            "DOP" => [
                "name" => "Dominican Peso",
                "conversion_rate" => 1,
                "symbol" => "RD$",
                "symbol_native" => "RD$",
                "status" => 0
            ],
            "DZD" => [
                "name" => "Algerian Dinar",
                "conversion_rate" => 1,
                "symbol" => "DA",
                "symbol_native" => "\u062f.\u062c.\u200f",
                "status" => 0
            ],
            "EEK" => [
                "name" => "Estonian Kroon",
                "conversion_rate" => 1,
                "symbol" => "Ekr",
                "symbol_native" => "kr",
                "status" => 0
            ],
            "EGP" => [
                "name" => "Egyptian Pound",
                "conversion_rate" => 1,
                "symbol" => "EGP",
                "symbol_native" => "\u062c.\u0645.\u200f",
                "status" => 0
            ],
            "ERN" => [
                "name" => "Eritrean Nakfa",
                "conversion_rate" => 1,
                "symbol" => "Nfk",
                "symbol_native" => "Nfk",
                "status" => 0
            ],
            "ETB" => [
                "name" => "Ethiopian Birr",
                "conversion_rate" => 1,
                "symbol" => "Br",
                "symbol_native" => "Br",
                "status" => 0
            ],
            "GBP" => [
                "name" => "British Pound Sterling",
                "conversion_rate" => 1,
                "symbol" => "\u00a3",
                "symbol_native" => "\u00a3",
                "status" => 0
            ],
            "GEL" => [
                "name" => "Georgian Lari",
                "conversion_rate" => 1,
                "symbol" => "GEL",
                "symbol_native" => "GEL",
                "status" => 0
            ],
            "GHS" => [
                "name" => "Ghanaian Cedi",
                "conversion_rate" => 1,
                "symbol" => "GH\u20b5",
                "symbol_native" => "GH\u20b5",
                "status" => 0
            ],
            "GNF" => [
                "name" => "Guinean Franc",
                "conversion_rate" => 1,
                "symbol" => "FG",
                "symbol_native" => "FG",
                "status" => 0
            ],
            "GTQ" => [
                "name" => "Guatemalan Quetzal",
                "conversion_rate" => 1,
                "symbol" => "GTQ",
                "symbol_native" => "Q",
                "status" => 0
            ],
            "HKD" => [
                "name" => "Hong Kong Dollar",
                "conversion_rate" => 1,
                "symbol" => "HK$",
                "symbol_native" => "$",
                "status" => 0
            ],
            "HNL" => [
                "name" => "Honduran Lempira",
                "conversion_rate" => 1,
                "symbol" => "HNL",
                "symbol_native" => "L",
                "status" => 0
            ],
            "HRK" => [
                "name" => "Croatian Kuna",
                "conversion_rate" => 1,
                "symbol" => "kn",
                "symbol_native" => "kn",
                "status" => 0
            ],
            "HUF" => [
                "name" => "Hungarian Forint",
                "conversion_rate" => 1,
                "symbol" => "Ft",
                "symbol_native" => "Ft",
                "status" => 0
            ],
            "IDR" => [
                "name" => "Indonesian Rupiah",
                "conversion_rate" => 1,
                "symbol" => "Rp",
                "symbol_native" => "Rp",
                "status" => 0
            ],
            "ILS" => [
                "name" => "Israeli New Sheqel",
                "conversion_rate" => 1,
                "symbol" => "\u20aa",
                "symbol_native" => "\u20aa",
                "status" => 0
            ],
            "INR" => [
                "name" => "Indian Rupee",
                "conversion_rate" => 1,
                "symbol" => "Rs",
                "symbol_native" => "\u20b9",
                "status" => 0
            ],
            "IQD" => [
                "name" => "Iraqi Dinar",
                "conversion_rate" => 1,
                "symbol" => "IQD",
                "symbol_native" => "\u062f.\u0639.\u200f",
                "status" => 0
            ],
            "IRR" => [
                "name" => "Iranian Rial",
                "conversion_rate" => 1,
                "symbol" => "IRR",
                "symbol_native" => "\ufdfc",
                "status" => 0
            ],
            "ISK" => [
                "name" => "Icelandic Kr\u00f3na",
                "conversion_rate" => 1,
                "symbol" => "Ikr",
                "symbol_native" => "kr",
                "status" => 0
            ],
            "JMD" => [
                "name" => "Jamaican Dollar",
                "conversion_rate" => 1,
                "symbol" => "J$",
                "symbol_native" => "$",
                "status" => 0
            ],
            "JOD" => [
                "name" => "Jordanian Dinar",
                "conversion_rate" => 1,
                "symbol" => "JD",
                "symbol_native" => "\u062f.\u0623.\u200f",
                "status" => 0
            ],
            "JPY" => [
                "name" => "Japanese Yen",
                "conversion_rate" => 1,
                "symbol" => "\u00a5",
                "symbol_native" => "\uffe5",
                "status" => 0
            ],
            "KES" => [
                "name" => "Kenyan Shilling",
                "conversion_rate" => 1,
                "symbol" => "KSh",
                "symbol_native" => "KSh",
                "status" => 0
            ],
            "KHR" => [
                "name" => "Cambodian Riel",
                "conversion_rate" => 1,
                "symbol" => "KHR",
                "symbol_native" => "\u17db",
                "status" => 0
            ],
            "KMF" => [
                "name" => "Comorian Franc",
                "conversion_rate" => 1,
                "symbol" => "CF",
                "symbol_native" => "FC",
                "status" => 0
            ],
            "KRW" => [
                "name" => "South Korean Won",
                "conversion_rate" => 1,
                "symbol" => "\u20a9",
                "symbol_native" => "\u20a9",
                "status" => 0
            ],
            "KWD" => [
                "name" => "Kuwaiti Dinar",
                "conversion_rate" => 1,
                "symbol" => "KD",
                "symbol_native" => "\u062f.\u0643.\u200f",
                "status" => 0
            ],
            "KZT" => [
                "name" => "Kazakhstani Tenge",
                "conversion_rate" => 1,
                "symbol" => "KZT",
                "symbol_native" => "\u0442\u04a3\u0433.",
                "status" => 0
            ],
            "LBP" => [
                "name" => "Lebanese Pound",
                "conversion_rate" => 1,
                "symbol" => "L.L.",
                "symbol_native" => "\u0644.\u0644.\u200f",
                "status" => 0
            ],
            "LKR" => [
                "name" => "Sri Lankan Rupee",
                "conversion_rate" => 1,
                "symbol" => "SLRs",
                "symbol_native" => "SL Re",
                "status" => 0
            ],
            "LTL" => [
                "name" => "Lithuanian Litas",
                "conversion_rate" => 1,
                "symbol" => "Lt",
                "symbol_native" => "Lt",
                "status" => 0
            ],
            "LVL" => [
                "name" => "Latvian Lats",
                "conversion_rate" => 1,
                "symbol" => "Ls",
                "symbol_native" => "Ls",
                "status" => 0
            ],
            "LYD" => [
                "name" => "Libyan Dinar",
                "conversion_rate" => 1,
                "symbol" => "LD",
                "symbol_native" => "\u062f.\u0644.\u200f",
                "status" => 0
            ],
            "MAD" => [
                "name" => "Moroccan Dirham",
                "conversion_rate" => 1,
                "symbol" => "MAD",
                "symbol_native" => "\u062f.\u0645.\u200f",
                "status" => 0
            ],
            "MDL" => [
                "name" => "Moldovan Leu",
                "conversion_rate" => 1,
                "symbol" => "MDL",
                "symbol_native" => "MDL",
                "status" => 0
            ],
            "MGA" => [
                "name" => "Malagasy Ariary",
                "conversion_rate" => 1,
                "symbol" => "MGA",
                "symbol_native" => "MGA",
                "status" => 0
            ],
            "MKD" => [
                "name" => "Macedonian Denar",
                "conversion_rate" => 1,
                "symbol" => "MKD",
                "symbol_native" => "MKD",
                "status" => 0
            ],
            "MMK" => [
                "name" => "Myanma Kyat",
                "conversion_rate" => 1,
                "symbol" => "MMK",
                "symbol_native" => "K",
                "status" => 0
            ],
            "MOP" => [
                "name" => "Macanese Pataca",
                "conversion_rate" => 1,
                "symbol" => "MOP$",
                "symbol_native" => "MOP$",
                "status" => 0
            ],
            "MUR" => [
                "name" => "Mauritian Rupee",
                "conversion_rate" => 1,
                "symbol" => "MURs",
                "symbol_native" => "MURs",
                "status" => 0
            ],
            "MXN" => [
                "name" => "Mexican Peso",
                "conversion_rate" => 1,
                "symbol" => "MX$",
                "symbol_native" => "$",
                "status" => 0
            ],
            "MYR" => [
                "name" => "Malaysian Ringgit",
                "conversion_rate" => 1,
                "symbol" => "RM",
                "symbol_native" => "RM",
                "status" => 0
            ],
            "MZN" => [
                "name" => "Mozambican Metical",
                "conversion_rate" => 1,
                "symbol" => "MTn",
                "symbol_native" => "MTn",
                "status" => 0
            ],
            "NAD" => [
                "name" => "Namibian Dollar",
                "conversion_rate" => 1,
                "symbol" => "N$",
                "symbol_native" => "N$",
                "status" => 0
            ],
            "NGN" => [
                "name" => "Nigerian Naira",
                "conversion_rate" => 1,
                "symbol" => "\u20a6",
                "symbol_native" => "\u20a6",
                "status" => 0
            ],
            "NIO" => [
                "name" => "Nicaraguan C\u00f3rdoba",
                "conversion_rate" => 1,
                "symbol" => "C$",
                "symbol_native" => "C$",
                "status" => 0
            ],
            "NOK" => [
                "name" => "Norwegian Krone",
                "conversion_rate" => 1,
                "symbol" => "Nkr",
                "symbol_native" => "kr",
                "status" => 0
            ],
            "NPR" => [
                "name" => "Nepalese Rupee",
                "conversion_rate" => 1,
                "symbol" => "NPRs",
                "symbol_native" => "\u0928\u0947\u0930\u0942",
                "status" => 0
            ],
            "NZD" => [
                "name" => "New Zealand Dollar",
                "conversion_rate" => 1,
                "symbol" => "NZ$",
                "symbol_native" => "$",
                "status" => 0
            ],
            "OMR" => [
                "name" => "Omani Rial",
                "conversion_rate" => 1,
                "symbol" => "OMR",
                "symbol_native" => "\u0631.\u0639.\u200f",
                "status" => 0
            ],
            "PAB" => [
                "name" => "Panamanian Balboa",
                "conversion_rate" => 1,
                "symbol" => "B\/.",
                "symbol_native" => "B\/.",
                "status" => 0
            ],
            "PEN" => [
                "name" => "Peruvian Nuevo Sol",
                "conversion_rate" => 1,
                "symbol" => "S\/.",
                "symbol_native" => "S\/.",
                "status" => 0
            ],
            "PHP" => [
                "name" => "Philippine Peso",
                "conversion_rate" => 1,
                "symbol" => "\u20b1",
                "symbol_native" => "\u20b1",
                "status" => 0
            ],
            "PKR" => [
                "name" => "Pakistani Rupee",
                "conversion_rate" => 1,
                "symbol" => "PKRs",
                "symbol_native" => "\u20a8",
                "status" => 0
            ],
            "PLN" => [
                "name" => "Polish Zloty",
                "conversion_rate" => 1,
                "symbol" => "z\u0142",
                "symbol_native" => "z\u0142",
                "status" => 0
            ],
            "PYG" => [
                "name" => "Paraguayan Guarani",
                "conversion_rate" => 1,
                "symbol" => "\u20b2",
                "symbol_native" => "\u20b2",
                "status" => 0
            ],
            "QAR" => [
                "name" => "Qatari Rial",
                "conversion_rate" => 1,
                "symbol" => "QR",
                "symbol_native" => "\u0631.\u0642.\u200f",
                "status" => 0
            ],
            "RON" => [
                "name" => "Romanian Leu",
                "conversion_rate" => 1,
                "symbol" => "RON",
                "symbol_native" => "RON",
                "status" => 0
            ],
            "RSD" => [
                "name" => "Serbian Dinar",
                "conversion_rate" => 1,
                "symbol" => "din.",
                "symbol_native" => "\u0434\u0438\u043d.",
                "status" => 0
            ],
            "RUB" => [
                "name" => "Russian Ruble",
                "conversion_rate" => 1,
                "symbol" => "RUB",
                "symbol_native" => "\u20bd.",
                "status" => 0
            ],
            "RWF" => [
                "name" => "Rwandan Franc",
                "conversion_rate" => 1,
                "symbol" => "RF",
                "symbol_native" => "FR",
                "status" => 0
            ],
            "SAR" => [
                "name" => "Saudi Riyal",
                "conversion_rate" => 1,
                "symbol" => "SR",
                "symbol_native" => "\u0631.\u0633.\u200f",
                "status" => 0
            ],
            "SDG" => [
                "name" => "Sudanese Pound",
                "conversion_rate" => 1,
                "symbol" => "SDG",
                "symbol_native" => "SDG",
                "status" => 0
            ],
            "SEK" => [
                "name" => "Swedish Krona",
                "conversion_rate" => 1,
                "symbol" => "Skr",
                "symbol_native" => "kr",
                "status" => 0
            ],
            "SGD" => [
                "name" => "Singapore Dollar",
                "conversion_rate" => 1,
                "symbol" => "S$",
                "symbol_native" => "$",
                "status" => 0
            ],
            "SOS" => [
                "name" => "Somali Shilling",
                "conversion_rate" => 1,
                "symbol" => "Ssh",
                "symbol_native" => "Ssh",
                "status" => 0
            ],
            "SYP" => [
                "name" => "Syrian Pound",
                "conversion_rate" => 1,
                "symbol" => "SY\u00a3",
                "symbol_native" => "\u0644.\u0633.\u200f",
                "status" => 0
            ],
            "THB" => [
                "name" => "Thai Baht",
                "conversion_rate" => 1,
                "symbol" => "\u0e3f",
                "symbol_native" => "\u0e3f",
                "status" => 0
            ],
            "TND" => [
                "name" => "Tunisian Dinar",
                "conversion_rate" => 1,
                "symbol" => "DT",
                "symbol_native" => "\u062f.\u062a.\u200f",
                "status" => 0
            ],
            "TOP" => [
                "name" => "Tongan Pa\u02bbanga",
                "conversion_rate" => 1,
                "symbol" => "T$",
                "symbol_native" => "T$",
                "status" => 0
            ],
            "TRY" => [
                "name" => "Turkish Lira",
                "conversion_rate" => 1,
                "symbol" => "TL",
                "symbol_native" => "TL",
                "status" => 0
            ],
            "TTD" => [
                "name" => "Trinidad and Tobago Dollar",
                "conversion_rate" => 1,
                "symbol" => "TT$",
                "symbol_native" => "$",
                "status" => 0
            ],
            "TWD" => [
                "name" => "New Taiwan Dollar",
                "conversion_rate" => 1,
                "symbol" => "NT$",
                "symbol_native" => "NT$",
                "status" => 0
            ],
            "TZS" => [
                "name" => "Tanzanian Shilling",
                "conversion_rate" => 1,
                "symbol" => "TSh",
                "symbol_native" => "TSh",
                "status" => 0
            ],
            "UAH" => [
                "name" => "Ukrainian Hryvnia",
                "conversion_rate" => 1,
                "symbol" => "\u20b4",
                "symbol_native" => "\u20b4",
                "status" => 0
            ],
            "UGX" => [
                "name" => "Ugandan Shilling",
                "conversion_rate" => 1,
                "symbol" => "USh",
                "symbol_native" => "USh",
                "status" => 0
            ],
            "UYU" => [
                "name" => "Uruguayan Peso",
                "conversion_rate" => 1,
                "symbol" => "\$U",
                "symbol_native" => "$",
                "status" => 0
            ],
            "UZS" => [
                "name" => "Uzbekistan Som",
                "conversion_rate" => 1,
                "symbol" => "UZS",
                "symbol_native" => "UZS",
                "status" => 0
            ],
            "VEF" => [
                "name" => "Venezuelan Bol\u00edvar",
                "conversion_rate" => 1,
                "symbol" => "Bs.F.",
                "symbol_native" => "Bs.F.",
                "status" => 0
            ],
            "VND" => [
                "name" => "Vietnamese Dong",
                "conversion_rate" => 1,
                "symbol" => "\u20ab",
                "symbol_native" => "\u20ab",
                "status" => 0
            ],
            "XAF" => [
                "name" => "CFA Franc BEAC",
                "conversion_rate" => 1,
                "symbol" => "FCFA",
                "symbol_native" => "FCFA",
                "status" => 0
            ],
            "XOF" => [
                "name" => "CFA Franc BCEAO",
                "conversion_rate" => 1,
                "symbol" => "CFA",
                "symbol_native" => "CFA",
                "status" => 0
            ],
            "YER" => [
                "name" => "Yemeni Rial",
                "conversion_rate" => 1,
                "symbol" => "YR",
                "symbol_native" => "\u0631.\u064a.\u200f",
                "status" => 0
            ],
            "ZAR" => [
                "name" => "South African Rand",
                "conversion_rate" => 1,
                "symbol" => "R",
                "symbol_native" => "R",
                "status" => 0
            ],
            "ZMK" => [
                "name" => "Zambian Kwacha",
                "conversion_rate" => 1,
                "symbol" => "ZK",
                "symbol_native" => "ZK",
                "status" => 0
            ],
            "ZWL" => [
                "name" => "Zimbabwean Dollar",
                "conversion_rate" => 1,
                "symbol" => "ZWL$",
                "symbol_native" => "ZWL$",
                "status" => 0
            ],
            "LSL" => [
                "name" => "Lesotho",
                "conversion_rate" => 1,
                "symbol" => "M",
                "symbol_native" => "M",
                "status" => 0
            ],
            "SZL" => [
                "name" => "Eswatini Lilangeni",
                "conversion_rate" => 1,
                "symbol" => "E",
                "symbol_native" => "E",
                "status" => 0
            ]

        ];

        foreach($insert as $key=>$value){
            $text = $value['symbol'];
            // The regular expression to match Unicode code point escapes
            $regex = '/\\\\u([0-9a-fA-F]{4})/';

            // Replace the Unicode code point escapes with their corresponding UTF-8 characters
            $decoded = preg_replace_callback($regex, function ($match) {
                    return mb_convert_encoding('&#x' . $match[1] . ';', 'UTF-8', 'HTML-ENTITIES');
            }, $text);

            $value['symbol'] = $decoded;
        
            \DB::table('currencies')->insert($value);
        }
    }
}
