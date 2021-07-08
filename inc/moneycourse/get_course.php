<?php
//echo get_currency('USD', 3);
//AUD – 1 австралийский доллар
//AZN – 1 азербайджанский манат
//GBP – 1 фунт стерлингов Соединенного королевства
//AMD – 100 армянских драмов
//BYN – 1 белорусский рубль
//BGN – 1 болгарский лев
//BRL – 1 бразильский реал
//HUF – 100 венгерских форинтов
//HKD – 10 гонконгских долларов
//DKK – 10 датских крон
//USD – 1 доллар США
//EUR – 1 евро
//INR – 100 индийских рупий
//KZT – 100 казахстанских тенге
//CAD – 1 канадский доллар
//KGS – 100 киргизских сомов
//CNY – 10 китайских юаней
//MDL – 10 молдавских леев
//NOK – 10 норвежских крон
//PLN – 1 польский злотый
//RON – 1 румынский лей
//XDR – 1 СДР (специальные права заимствования)
//SGD – 1 сингапурский доллар
//TJS – 10 таджикских сомони
//TRY – 1 турецкая лира
//TMT – 1 новый туркменский манат
//UZS – 10 000 узбекских сумов
//UAH – 10 украинских гривен
//CZK – 10 чешских крон
//SEK - 10 шведских крон
//CHF – 1 швейцарский франк
//ZAR – 10 южноафриканских рэндов
//KRW – 1 000 вон Республики Корея

function get_currency($currency_code, $format) {
    $date = date('d/m/Y'); // Текущая дата
    $cache_time_out = '3600'; // Время жизни кэша в секундах
    $file_currency_cache = __DIR__.'/XML_daily.asp';
    if(!is_file($file_currency_cache) || filemtime($file_currency_cache) < (time() - $cache_time_out)) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.cbr.ru/scripts/XML_daily.asp?date_req='.$date);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $out = curl_exec($ch);
        curl_close($ch);
        file_put_contents($file_currency_cache, $out);
    }
    $content_currency = simplexml_load_file($file_currency_cache);
    return number_format(str_replace(',', '.', $content_currency->xpath('Valute[CharCode="'.$currency_code.'"]')[0]->Value), $format);
}