<?php

declare(strict_types=1);

namespace App\Service;

use Psr\Log\LoggerInterface;
use WebSocket\Client;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

// https://developers.binance.com/docs/derivatives/usds-margined-futures/websocket-market-streams
class BinanceWebSocketService
{
    private Client $client;
    public function __construct(
        private HubInterface $hub,
        private LoggerInterface $logger
    ) {
    }

    private string $wsUrl = "wss://fstream.binance.com/stream?streams=btcusdt@trade/ethusdt@trade/bnbusdt@trade";

    public function listen(): void
    {
        $cache = [];
        $cooldownMs = 3000;
        try {
            $this->client = new Client($this->wsUrl);

            /*
             * {"stream":"ethusdt@trade","data":{"e":"trade","E":1743166595666,"T":1743166595666,"s":"ETHUSDT","t":5404710166,"p":"1889.80","q":"0.128","X":"MARKET","m":true}}
             * {"stream":"btcusdt@trade","data":{"e":"trade","E":1743166595259,"T":1743166595258,"s":"BTCUSDT","t":6141979216,"p":"85118.90","q":"0.002","X":"MARKET","m":false}}
             * {"stream":"bnbusdt@trade","data":{"e":"trade","E":1743166663179,"T":1743166663178,"s":"BNBUSDT","t":1649099777,"p":"628.900","q":"0.01","X":"MARKET","m":true}}
             */
            while (true) {
                $message = $this->client->receive();

                $data = json_decode($message->getContent(), true);

                if (is_array($data) && array_key_exists('data', $data) && $data['data'] != null) {
                    $now = microtime(true) * 1000; // current time in ms
                    $tradeData = $data['data'];
                    $market = $tradeData['s'];
                    $tradeTime = $this->convertTimestampToDt($tradeData['T']);

                    if (!isset($cache[$market]) || ($now - $cache[$market] >= $cooldownMs))
                    {
                        $cache[$market] = $now;
                        $this->pushRateToMercure($market, $tradeData['p']);
                        $this->pushTradeDataToMercure($market, $tradeData['p'], $tradeTime);
                    }
                }
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            $this->logger->error($e->getTraceAsString());
            $this->logger->error(strval($e->getCode()));
        } finally {
            if ($this->client->isConnected()) {
                $this->client->close();
            }
        }
    }
    public function pushToMercure(string $topic, string $data): void
    {
        $update = new Update($topic, $data);
        $this->hub->publish($update);
    }

    public function pushRateToMercure(string $market, string $rate): void
    {
        $topic = sprintf("https://example.com/exchange-rate/%s", $market);
        $data = json_encode([
            'rate' => $rate,
        ]);
        $this->pushToMercure($topic, $data);
    }

    public function pushTradeDataToMercure(string $market, string $rate, string $tradeTime): void
    {
        $topic = "https://example.com/exchange-rates";
        $data = json_encode([
            'market'   => $market,
            'rate'    => $rate,
            'tradeTime' => $tradeTime
        ]);
        $this->pushToMercure($topic, $data);
    }

    function convertTimestampToDt($timestampMs): string {
        $seconds = $timestampMs / 1000;
        $dt = (new \DateTime())->setTimestamp((int) $seconds);
        return $dt->format('Y-m-d H:i:s');
    }
}
