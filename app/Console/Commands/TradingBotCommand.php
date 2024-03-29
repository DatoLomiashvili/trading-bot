<?php

namespace App\Console\Commands;

use App\Models\TradingBot;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TradingBotCommand extends Command
{
    private TradingBot $tradingBot;
    protected $signature = 'trading-bot {action}';
    protected $description = 'Perform trading bot actions';
    protected array $actions = ["activate", "deactivate", "make_decision"];

    public function __construct(TradingBot $tradingBot)
    {
        parent::__construct();
        $this->tradingBot = $tradingBot->first();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $action = $this->argument('action');

        switch ($action) {
            case 'activate':
                $this->activateBot();
                $this->scheduleMakeDecision();
                break;
            case 'deactivate':
                $this->deactivateBot();
                break;
            case 'make_decision':
                $this->makeTradeDecision();
                break;
            default:
                $this->error("Invalid action! Permissible actions are: " . implode(', ', $this->actions));
                break;
        }
    }

    /**
     * Activate the bot and schedule the make_decision command.
     */
    protected function activateBot(): void
    {
        $this->tradingBot['active'] = true;
        $this->tradingBot->save();
        $this->info('Bot activated successfully.');
        Log::info('Bot activated successfully.');
    }

    /**
     * Deactivate the bot and unschedule the make_decision command.
     */
    protected function deactivateBot(): void
    {
        $this->tradingBot['active'] = false;
        $this->tradingBot->save();
        $this->info('Bot deactivated successfully.');
        Log::info('Bot deactivated successfully.');
    }

    /**
     * Make trade decision placeholder.
     */
    protected function makeTradeDecision(): void
    {
        $client = new Client();

        try {
            // Make an HTTP POST request to your API endpoint
            $response = $client->get('localhost:8000/api/trading-bot/cross-strategy');
            $content = $response->getBody()->getContents();

            $this->info('preferred action: ' . $content);
            $this->line('preferred action: ' . $content);
            Log::info('preferred action: ' . $content);
        } catch (\Exception $e) {
            $this->error("Error occurred: " . $e->getMessage());
            Log::info('error: ' . $e->getMessage());
        }

        $this->info('Make decision command executed.');
        Log::info('Make decision command executed.');
    }

    /**
     * Placeholder method to check if the bot is active.
     */
    protected function isActive(): bool
    {
        return $this->tradingBot['active'];
    }

    protected function scheduleMakeDecision(): void
    {
        // Schedule the makeTradeDecision command every 5 seconds
        $this->info('Scheduling makeTradeDecision every 5 seconds.');
        Log::info('Scheduling makeTradeDecision every 5 seconds.');
        $this->call('schedule:run');
    }
}
