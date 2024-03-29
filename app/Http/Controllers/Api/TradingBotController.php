<?php
namespace App\Http\Controllers\Api;

use App\Request\TradingBotRequest;
use App\Services\TradingBotService;
use App\Supports\ResponseSupport;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Foundation\Validation\ValidatesRequests;

class TradingBotController extends ApiController
{
    use ResponseSupport;
    use ValidatesRequests;

    /**
     * @OA\Get (
     *     path="/api/trading-bot/cross-strategy",
     *     tags={"Trading Bot"},
     *     summary="",
     *     @OA\Parameter(
     *            name="crypto_coin",
     *            @OA\Schema(type="string"),
     *            in="query",
     *            description="crypto coin",
     *            example="BTC",
     *            required=true,
     *      ),
     *     @OA\Parameter(
     *           name="low_moving_average",
     *           @OA\Schema(type="integer"),
     *           in="query",
     *           description="low moving average",
     *           example="50",
     *           required=true,
     *     ),
     *     @OA\Parameter(
     *            name="high_moving_average",
     *            @OA\Schema(type="string"),
     *            in="query",
     *            description="high moving average",
     *            example="200",
     *            required=false,
     *      ),
     *     @OA\Response(
     *         response="400",
     *         description="bad request",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="success",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="not found",
     *         @OA\JsonContent()
     *     ),
     * )
     *
     * @param TradingBotRequest $request
     * @param TradingBotService $tradingBotService
     * @return string
     * @throws GuzzleException
     */
    public function crossStrategy(TradingBotRequest $request, TradingBotService $tradingBotService): string
    {
        try {
            return $tradingBotService->determineAction(
                cryptoCoin: $request->get(key: 'crypto_coin'),
                lowMovingAverageDays: $request->get(key: 'low_moving_average'),
                highMovingAverageDays: $request->get(key: 'high_moving_average'),
            );
        } catch (Exception $exception) {
            return $this->notFound($exception);
        }
    }
}
