<?php
/**
 * This file is part of TheCartPress.
 * 
 * TheCartPress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TheCartPress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TheCartPress.  If not, see <http://www.gnu.org/licenses/>.
 */

class Currencies {
	static function createTable() {
		global $wpdb;
		$sql = 'CREATE TABLE IF NOT EXISTS `' . $wpdb->prefix . 'tcp_currencies` (
			`iso`		char(3)			NOT NULL,
			`code`		int(4)			NOT NULL,
			`decimal`	int(1)			NOT NULL,
			`currency`	varchar(50)		NOT NULL,
			`entities`	varchar(300)	NOT NULL,
			PRIMARY KEY (`iso`)
		) ENGINE = MyISAM DEFAULT CHARSET=utf8;';
		$wpdb->query( $sql );
	}

	static function get( $iso ) {
		global $wpdb;
		return $wpdb->get_row( $wpdb->prepare( 'select * from ' . $wpdb->prefix . 'tcp_currencies where iso = %s', $iso ) );
	}

	static function getAll() {
		global $wpdb;
		$sql = 'select iso, currency from ' . $wpdb->prefix . 'tcp_currencies order by currency';
		return $wpdb->get_results( $sql );
	}

	static function initData() {
		global $wpdb;
		$count = $wpdb->get_var( 'select count(*) from ' . $wpdb->prefix . 'tcp_currencies' );
		if ( $count == 0 ) {
			$sql = 'INSERT INTO `' . $wpdb->prefix . 'tcp_currencies` VALUES 
			(\'AED\', 784, 2, \'United Arab Emirates dirham\', \'United Arab Emirates\'),
			(\'AFN\', 971, 2, \'Afghan afghani\', \'Afghanistan\'),
			(\'ALL\',   8, 2, \'Albanian lek\', \'Albania\'),
			(\'AMD\',  51, 2, \'Armenian dram\', \'Armenia\'),
			(\'ANG\', 532, 2, \'Netherlands Antillean guilder\', \'Netherlands Antilles\'),
			(\'AOA\', 973, 2, \'Angolan kwanza\', \'Angola\'),
			(\'ARS\',  32, 2, \'Argentine peso\', \'Argentina\'),
			(\'AUD\',  36, 2, \'Australian dollar\', \'Australia, Australian Antarctic Territory, Christmas Island, Cocos (Keeling) Islands, Heard and McDonald Islands, Kiribati, Nauru, Norfolk Island, Tuvalu\'),
			(\'AWG\', 533, 2, \'Aruban guilder\', \'Aruba\'),
			(\'AZN\', 944, 2, \'Azerbaijani manat\', \'Azerbaijan\'),
			(\'BAM\', 977, 2, \'Bosnia and Herzegovina konvertibilna marka\', \'Bosnia and Herzegovina\'),
			(\'BBD\',  52, 2, \'Barbados dollar\', \'Barbados\'),
			(\'BDT\',  50, 2, \'Bangladeshi taka\', \'Bangladesh\'),
			(\'BGN\', 975, 2, \'Bulgarian lev\', \'Bulgaria\');';
			$wpdb->query( $sql );
			$sql = 'INSERT INTO `' . $wpdb->prefix . 'tcp_currencies` VALUES 
			(\'BHD\',  48, 3, \'Bahraini dinar\', \'Bahrain\'),
			(\'BIF\', 108, 0, \'Burundian franc\', \'Burundi\'),
			(\'BMD\',  60, 2, \'Bermudian dollar (customarily known as Bermuda dollar)\', \'Bermuda\'),
			(\'BND\',  96, 2, \'Brunei dollar\', \'Brunei, Singapore\'),
			(\'BOB\',  68, 2, \'Boliviano\', \'Bolivia\'),
			(\'BOV\', 984, 2, \'Bolivian Mvdol (funds code)\', \'Bolivia\'),
			(\'BRL\', 986, 2, \'Brazilian real\', \'Brazil\'),
			(\'BSD\',  44, 2, \'Bahamian dollar\', \'Bahamas\'),
			(\'BTN\',  64, 2, \'Bhutanese ngultrum\', \'Bhutan\'),
			(\'BWP\',  72, 2, \'Botswana pula\', \'Botswana\'),
			(\'BYR\', 974, 0, \'Belarusian ruble\', \'Belarus\'),
			(\'BZD\',  84, 2, \'Belize dollar\', \'Belize\'),
			(\'CAD\', 124, 2, \'Canadian dollar\', \'Canada\'),
			(\'CDF\', 976, 2, \'Congolese franc\', \'Democratic Republic of Congo\'),
			(\'CHE\', 947, 2, \'WIR Bank (complementary currency)\', \'Switzerland\'),
			(\'CHF\', 756, 2, \'Swiss franc\', \'Switzerland, Liechtenstein\'),
			(\'CHW\', 948, 2, \'WIR Bank (complementary currency)\', \'Switzerland\');';
			$wpdb->query( $sql );
			$sql = 'INSERT INTO `' . $wpdb->prefix . 'tcp_currencies` VALUES 
			(\'CLF\', 990, 0, \'Unidad de Fomento (funds code)\', \'Chile\'),
			(\'CLP\', 152, 0, \'Chilean peso\', \'Chile\'),
			(\'CNY\', 156, 1, \'Chinese yuan\', \'China (Mainland)\'),
			(\'COP\', 170, 0, \'Colombian peso\', \'Colombia\'),
			(\'COU\', 970, 2, \'Unidad de Valor Real\', \'Colombia\'),
			(\'CRC\', 188, 2, \'Costa Rican colon\', \'Costa Rica\'),
			(\'CUC\', 931, 2, \'Cuban convertible peso\', \'Cuba\'),
			(\'CUP\', 192, 2, \'Cuban peso\', \'Cuba\'),
			(\'CVE\', 132, 0, \'Cape Verde escudo\', \'Cape Verde\'),
			(\'CZK\', 203, 2, \'Czech koruna\', \'Czech Republic\'),
			(\'DJF\', 262, 0, \'Djiboutian franc\', \'Djibouti\'),
			(\'DKK\', 208, 2, \'Danish krone\', \'Denmark, Faroe Islands, Greenland\'),
			(\'DOP\', 214, 2, \'Dominican peso\', \'Dominican Republic\'),
			(\'DZD\',  12, 2, \'Algerian dinar\', \'Algeria\'),
			(\'EEK\', 233, 2, \'Estonian kroon\', \'Estonia\'),
			(\'EGP\', 818, 2, \'Egyptian pound\', \'Egypt\'),
			(\'ERN\', 232, 2, \'Eritrean nakfa\', \'Eritrea\'),
			(\'ETB\', 230, 2, \'Ethiopian birr\', \'Ethiopia\');';
			$wpdb->query( $sql );
			$sql = 'INSERT INTO `' . $wpdb->prefix . 'tcp_currencies` VALUES 
			(\'EUR\', 978, 2, \'Euro\', \'16 European Union countries, Andorra, Kosovo, Monaco, Montenegro, San Marino, Vatican City; see eurozone\'),
			(\'FJD\', 242, 2, \'Fiji dollar\', \'Fiji\'),
			(\'FKP\', 238, 2, \'Falkland Islands pound\', \'Falkland Islands\'),
			(\'GBP\', 826, 2, \'Pound sterling\', \'United Kingdom, Crown Dependencies (the Isle of Man and the Channel Islands), certain British Overseas Territories (South Georgia and the South Sandwich Islands, British Antarctic Territory and British Indian Ocean Territory)\'),
			(\'GEL\', 981, 2, \'Georgian lari\', \'Georgia\'),
			(\'GHS\', 936, 2, \'Ghanaian cedi\', \'Ghana\'),
			(\'GIP\', 292, 2, \'Gibraltar pound\', \'Gibraltar\'),
			(\'GMD\', 270, 2, \'Gambian dalasi\', \'Gambia\'),
			(\'GNF\', 324, 0, \'Guinean franc\', \'Guinea\'),
			(\'GTQ\', 320, 2, \'Guatemalan quetzal\', \'Guatemala\'),
			(\'GYD\', 328, 2, \'Guyanese dollar\', \'Guyana\'),
			(\'HKD\', 344, 2, \'Hong Kong dollar\', \'Hong Kong Special Administrative Region\'),
			(\'HNL\', 340, 2, \'Honduran lempira\', \'Honduras\'),
			(\'HRK\', 191, 2, \'Croatian kuna\', \'Croatia\');';
			$wpdb->query( $sql );
			$sql = 'INSERT INTO `' . $wpdb->prefix . 'tcp_currencies` VALUES 
			(\'HTG\', 332, 2, \'Haitian gourde\', \'Haiti\'),
			(\'HUF\', 348, 2, \'Hungarian forint\', \'Hungary\'),
			(\'IDR\', 360, 0, \'Indonesian rupiah\', \'Indonesia\'),
			(\'ILS\', 376, 2, \'Israeli new sheqel\', \'Israel\'),
			(\'INR\', 356, 2, \'Indian rupee\', \'Bhutan, India, Nepal\'),
			(\'IQD\', 368, 0, \'Iraqi dinar\', \'Iraq\'),
			(\'IRR\', 364, 0, \'Iranian rial\', \'Iran\'),
			(\'ISK\', 352, 0, \'Icelandic króna\', \'Iceland\'),
			(\'JMD\', 388, 2, \'Jamaican dollar\', \'Jamaica\'),
			(\'JOD\', 400, 3, \'Jordanian dinar\', \'Jordan\'),
			(\'JPY\', 392, 0, \'Japanese yen\', \'Japan\'),
			(\'KES\', 404, 2, \'Kenyan shilling\', \'Kenya\'),
			(\'KGS\', 417, 2, \'Kyrgyzstani som\', \'Kyrgyzstan\'),
			(\'KHR\', 116, 0, \'Cambodian riel\', \'Cambodia\'),
			(\'KMF\', 174, 0, \'Comoro franc\', \'Comoros\'),
			(\'KPW\', 408, 0, \'North Korean won\', \'North Korea\'),
			(\'KRW\', 410, 0, \'South Korean won\', \'South Korea\');';
			$wpdb->query( $sql );
			$sql = 'INSERT INTO `' . $wpdb->prefix . 'tcp_currencies` VALUES 
			(\'KWD\', 414, 3, \'Kuwaiti dinar\', \'Kuwait\'),
			(\'KYD\', 136, 2, \'Cayman Islands dollar\', \'Cayman Islands\'),
			(\'KZT\', 398, 2, \'Kazakhstani tenge\', \'Kazakhstan\'),
			(\'LAK\', 418, 0, \'Lao kip\', \'Laos\'),
			(\'LBP\', 422, 0, \'Lebanese pound\', \'Lebanon\'),
			(\'LKR\', 144, 2, \'Sri Lanka rupee\', \'Sri Lanka\'),
			(\'LRD\', 430, 2, \'Liberian dollar\', \'Liberia\'),
			(\'LSL\', 426, 2, \'Lesotho loti\', \'Lesotho\'),
			(\'LTL\', 440, 2, \'Lithuanian litas\', \'Lithuania\'),
			(\'LVL\', 428, 2, \'Latvian lats\', \'Latvia\'),
			(\'LYD\', 434, 3, \'Libyan dinar\', \'Libya\'),
			(\'MAD\', 504, 2, \'Moroccan dirham\', \'Morocco, Western Sahara\'),
			(\'MDL\', 498, 2, \'Moldovan leu\', \'Moldova (except Transnistria)\'),
			(\'MGA\', 969, 0, \'Malagasy ariary\', \'Madagascar\'),
			(\'MKD\', 807, 2, \'Macedonian denar\', \'Republic of Macedonia\'),
			(\'MMK\', 104, 0, \'Myanma kyat\', \'Myanmar\');';
			$wpdb->query( $sql );
			$sql = 'INSERT INTO `' . $wpdb->prefix . 'tcp_currencies` VALUES 
			(\'MNT\', 496, 2, \'Mongolian tugrik\', \'Mongolia\'),
			(\'MOP\', 446, 1, \'Macanese pataca\', \'Macau Special Administrative Region\'),
			(\'MRO\', 478, 0, \'Mauritanian ouguiya\', \'Mauritania\'),
			(\'MUR\', 480, 2, \'Mauritian rupee\', \'Mauritius\'),
			(\'MVR\', 462, 2, \'Maldivian rufiyaa\', \'Maldives\'),
			(\'MWK\', 454, 2, \'Malawian kwacha\', \'Malawi\'),
			(\'MXN\', 484, 2, \'Mexican peso\', \'Mexico\'),
			(\'MXV\', 979, 2, \'Mexican Unidad de Inversion (UDI) (funds code)\', \'Mexico\'),
			(\'MYR\', 458, 2, \'Malaysian ringgit\', \'Malaysia\'),
			(\'MZN\', 943, 2, \'Mozambican metical\', \'Mozambique\'),
			(\'NAD\', 516, 2, \'Namibian dollar\', \'Namibia\'),
			(\'NGN\', 566, 2, \'Nigerian naira\', \'Nigeria\'),
			(\'NIO\', 558, 2, \'Cordoba oro\', \'Nicaragua\');';
			$wpdb->query( $sql );
			$sql = 'INSERT INTO `' . $wpdb->prefix . 'tcp_currencies` VALUES 
			(\'NOK\', 578, 2, \'Norwegian krone\', \'Norway, Bouvet Island, Queen Maud Land, Peter I Island\'),
			(\'NPR\', 524, 2, \'Nepalese rupee\', \'Nepal\'),
			(\'NZD\', 554, 2, \'New Zealand dollar\', \'Cook Islands, New Zealand, Niue, Pitcairn, Tokelau\'),
			(\'OMR\', 512, 3, \'Omani rial\', \'Oman\'),
			(\'PAB\', 590, 2, \'Panamanian balboa\', \'Panama\'),
			(\'PEN\', 604, 2, \'Peruvian nuevo sol\', \'Peru\'),
			(\'PGK\', 598, 2, \'Papua New Guinean kina\', \'Papua New Guinea\'),
			(\'PHP\', 608, 2, \'Philippine peso\', \'Philippines\'),
			(\'PKR\', 586, 2, \'Pakistani rupee\', \'Pakistan\'),
			(\'PLN\', 985, 2, \'Polish złoty\', \'Poland\'),
			(\'PYG\', 600, 0, \'Paraguayan guaraní\', \'Paraguay\'),
			(\'QAR\', 634, 2, \'Qatari rial\', \'Qatar\'),
			(\'RON\', 946, 2, \'Romanian new leu\', \'Romania\'),
			(\'RSD\', 941, 2, \'Serbian dinar\', \'Serbia\'),
			(\'RUB\', 643, 2, \'Russian rouble\', \'Russia, Abkhazia, South Ossetia\');';
			$wpdb->query( $sql );
			$sql = 'INSERT INTO `' . $wpdb->prefix . 'tcp_currencies` VALUES 
			(\'RWF\', 646, 0, \'Rwandan franc\', \'Rwanda\'),
			(\'SAR\', 682, 2, \'Saudi riyal\', \'Saudi Arabia\'),
			(\'SBD\',  90, 2, \'Solomon Islands dollar\', \'Solomon Islands\'),
			(\'SCR\', 690, 2, \'Seychelles rupee\', \'Seychelles\'),
			(\'SDG\', 938, 2, \'Sudanese pound\', \'Sudan\'),
			(\'SEK\', 752, 2, \'Swedish krona/kronor\', \'Sweden\'),
			(\'SGD\', 702, 2, \'Singapore dollar\', \'Singapore, Brunei\'),
			(\'SHP\', 654, 2, \'Saint Helena pound\', \'Saint Helena\'),
			(\'SLL\', 694, 0, \'Sierra Leonean leone\', \'Sierra Leone\'),
			(\'SOS\', 706, 2, \'Somali shilling\', \'Somalia (except Somaliland)\'),
			(\'SRD\', 968, 2, \'Surinamese dollar\', \'Suriname\'),
			(\'STD\', 678, 0, \'São Tomé and Príncipe dobra\', \'São Tomé and Príncipe\'),
			(\'SYP\', 760, 2, \'Syrian pound\', \'Syria\'),
			(\'SZL\', 748, 2, \'Lilangeni\', \'Swaziland\'),
			(\'THB\', 764, 2, \'Thai baht\', \'Thailand\');';
			$wpdb->query( $sql );
			$sql = 'INSERT INTO `' . $wpdb->prefix . 'tcp_currencies` VALUES 
			(\'TJS\', 972, 2, \'Tajikistani somoni\', \'Tajikistan\'),
			(\'TMT\', 934, 2, \'Turkmenistani manat\', \'Turkmenistan\'),
			(\'TND\', 788, 3, \'Tunisian dinar\', \'Tunisia\'),
			(\'TOP\', 776, 2, \'Tongan paʻanga\', \'Tonga\'),
			(\'TRY\', 949, 2, \'Turkish lira\', \'Turkey, Northern Cyprus\'),
			(\'TTD\', 780, 2, \'Trinidad and Tobago dollar\', \'Trinidad and Tobago\'),
			(\'TWD\', 901, 1, \'New Taiwan dollar\', \'Taiwan and other islands that are under the effective control of the Republic of China (ROC)\'),
			(\'TZS\', 834, 2, \'Tanzanian shilling\', \'Tanzania\'),
			(\'UAH\', 980, 2, \'Ukrainian hryvnia\', \'Ukraine\'),
			(\'UGX\', 800, 0, \'Ugandan shilling\', \'Uganda\');';
			$wpdb->query( $sql );
			$sql = 'INSERT INTO `' . $wpdb->prefix . 'tcp_currencies` VALUES 
			(\'USD\', 840, 2, \'United States dollar\', \'American Samoa, British Indian Ocean Territory, Ecuador, El Salvador, Guam, Haiti, Marshall Islands, Micronesia, Northern Mariana Islands, Palau, Panama, Puerto Rico, Timor-Leste, Turks and Caicos Islands, United States, Virgin Islands, Bermuda (as well as Bermudian Dollar)\'),
			(\'USN\', 997, 2, \'United States dollar (next day) (funds code)\', \'United States\'),
			(\'USS\', 998, 2, \'United States dollar (same day) (funds code) (one source[who?] claims it is no longer used, but it is still on the ISO 4217-MA list)\', \'United States\'),
			(\'UYU\', 858, 2, \'Uruguayan peso\', \'Uruguay\'),
			(\'UZS\', 860, 2, \'Uzbekistan som\', \'Uzbekistan\'),
			(\'VEF\', 937, 2, \'Venezuelan bolívar fuerte\', \'Venezuela\'),
			(\'VND\', 704, 0, \'Vietnamese đồng\', \'Vietnam\'),
			(\'VUV\', 548, 0, \'Vanuatu vatu\', \'Vanuatu\'),
			(\'WST\', 882, 2, \'Samoan tala\', \'Samoa\');';
			$wpdb->query( $sql );
			$sql = 'INSERT INTO `' . $wpdb->prefix . 'tcp_currencies` VALUES 
			(\'XAF\', 950, 0, \'CFA franc BEAC\', \'Cameroon, Central African Republic, Republic of the Congo, Chad, Equatorial Guinea, Gabon\'),
			(\'XAG\', 961, 0, \'Silver (one troy ounce)\', \'\'),
			(\'XAU\', 959, 0, \'Gold (one troy ounce)\', \'\'),
			(\'XBA\', 955, 0, \'European Composite Unit (EURCO) (bond market unit)\', \'\'),
			(\'XBB\', 956, 0, \'European Monetary Unit (E.M.U.-6) (bond market unit)\', \'\'),
			(\'XBC\', 957, 0, \'European Unit of Account 9 (E.U.A.-9) (bond market unit)\', \'\'),
			(\'XBD\', 958, 0, \'European Unit of Account 17 (E.U.A.-17) (bond market unit)\', \'\');';
			$wpdb->query( $sql );
			$sql = 'INSERT INTO `' . $wpdb->prefix . 'tcp_currencies` VALUES 
			(\'XCD\', 951, 2, \'East Caribbean dollar\', \'Anguilla, Antigua and Barbuda, Dominica, Grenada, Montserrat, Saint Kitts and Nevis, Saint Lucia, Saint Vincent and the Grenadines\'),
			(\'XDR\', 960, 0, \'Special Drawing Rights\', \'International Monetary Fund\'),
			(\'XFU\',   0, 0, \'UIC franc (special settlement currency)\', \'International Union of Railways\'),
			(\'XOF\', 952, 0, \'CFA Franc BCEAO\', \'Benin, Burkina Faso, Côte d`Ivoire, Guinea-Bissau, Mali, Niger, Senegal, Togo\'),
			(\'XPD\', 964, 0, \'Palladium (one troy ounce)\', \'\');';
			$wpdb->query( $sql );
			$sql = 'INSERT INTO `' . $wpdb->prefix . 'tcp_currencies` VALUES 
			(\'XPF\', 953, 0, \'CFP franc\', \'French Polynesia, New Caledonia, Wallis and Futuna\'),
			(\'XPT\', 962, 0, \'Platinum (one troy ounce)\', \'\'),
			(\'XTS\', 963, 0, \'Code reserved for testing purposes\', \'\'),
			(\'XXX\', 999, 0, \'No currency\', \'\'),
			(\'YER\', 886, 0, \'Yemeni rial\', \'Yemen\'),
			(\'ZAR\', 710, 2, \'South African rand\', \'South Africa\'),
			(\'ZMK\', 894, 0, \'Zambian kwacha\', \'Zambia\'),
			(\'ZWL\', 932, 2, \'Zimbabwe dollar\', \'Zimbabwe\');';
			$wpdb->query( $sql );
		}
	}
}
?>
