<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class TradingBotService
{
    protected string $apiKey;
    protected Client $client;

    public function __construct()
    {
        $this->apiKey = env("ALPHA_VINTAGE_API_KEY");
        $this->client = new Client([
            'base_uri' => 'https://www.alphavantage.co/',
        ]);
    }

    // Function to fetch moving averages from Alpha Vantage API

    /**
     * @param $cryptoCoin
     * @param $lowMovingAverageDays
     * @param $highMovingAverageDays
     * @return array|null[]|string
     * @throws GuzzleException
     */
    public function fetchMovingAverages($cryptoCoin, $lowMovingAverageDays, $highMovingAverageDays): array|string
    {
        // fetch low moving averages from Alpha Vintage API
        $responseLow = $this->client->get("query?function=SMA&symbol={$cryptoCoin}" .
            "&interval=daily&" .
            "time_period={$lowMovingAverageDays}" .
            "&series_type=close&" .
            "apikey={$this->apiKey}");
        $dataLow = json_decode($responseLow->getBody()->getContents(), true);
        $lowMovingAverages = $dataLow['Technical Analysis: SMA'] ?? [];
        // fetch high moving averages from Alpha Vintage API
        $responseHigh = $this->client->get("query?function=SMA&symbol={$cryptoCoin}" .
            "&interval=daily&" .
            "time_period={$highMovingAverageDays}" .
            "&series_type=close&" .
            "apikey={$this->apiKey}");
        $dataHigh = json_decode($responseHigh->getBody()->getContents(), true);
        $highMovingAverages = $dataHigh['Technical Analysis: SMA'] ?? [];

        // Calculate average of low moving averages
        $totalLowMA = array_sum(array_column($lowMovingAverages, 'SMA'));
        $averageLowMA = count($lowMovingAverages) > 0 ? $totalLowMA / count($lowMovingAverages) : 0;

        // Calculate average of high moving averages
        $totalHighMA = array_sum(array_column($highMovingAverages, 'SMA'));
        $averageHighMA = count($highMovingAverages) > 0 ? $totalHighMA / count($highMovingAverages) : 0;
        return [
            'low_ma' => $averageLowMA,
            'high_ma' => $averageHighMA,
        ];
    }


    // Function to determine action based on moving averages

    /**
     * @param $cryptoCoin
     * @param $lowMovingAverageDays
     * @param $highMovingAverageDays
     * @return string
     * @throws GuzzleException
     */
    public function determineAction($cryptoCoin, $lowMovingAverageDays, $highMovingAverageDays): string
    {
        $movingAverages = $this->fetchMovingAverages($cryptoCoin, $lowMovingAverageDays, $highMovingAverageDays );
        if (!$movingAverages['low_ma'] || !$movingAverages['high_ma']) {
            return 'Data not available';
        }

        if ($movingAverages['low_ma'] > $movingAverages['high_ma']) {
            return 'Buy';
        } elseif ($movingAverages['low_ma'] < $movingAverages['high_ma']) {
            return 'Sell';
        } else {
            return 'Hold';
        }
    }
}