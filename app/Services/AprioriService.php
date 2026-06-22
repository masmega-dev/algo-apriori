<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\AprioriAnalysisRun;
use App\Models\AprioriRule;
use App\Models\Order;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

class AprioriService
{
    public function run(int $userId, CarbonInterface $from, CarbonInterface $to, float $minimumSupport, float $minimumConfidence, float $minimumLift = 0): AprioriAnalysisRun
    {
        return DB::transaction(function () use ($userId, $from, $to, $minimumSupport, $minimumConfidence, $minimumLift): AprioriAnalysisRun {
            $baskets = Order::query()->where('status', OrderStatus::Completed)->whereBetween('pickup_at', [$from->startOfDay(), $to->endOfDay()])->with('additionalItems:id,order_id,additional_item_id')->get(['id'])->map(fn (Order $order) => $order->additionalItems->pluck('additional_item_id')->unique()->sort()->values()->all())->filter()->values();
            $transactionCount = $baskets->count();
            $run = AprioriAnalysisRun::query()->create(['executed_by' => $userId, 'date_from' => $from->toDateString(), 'date_to' => $to->toDateString(), 'minimum_support' => $minimumSupport, 'minimum_confidence' => $minimumConfidence, 'minimum_lift' => $minimumLift, 'maximum_itemset' => 2, 'transaction_count' => $transactionCount, 'unique_item_count' => collect($baskets)->flatten()->unique()->count(), 'rule_count' => 0, 'executed_at' => now()]);
            if ($transactionCount === 0) {
                return $run;
            }
            $singles = []; $pairs = [];
            foreach ($baskets as $basket) { foreach ($basket as $item) { $singles[$item] = ($singles[$item] ?? 0) + 1; } for ($left = 0; $left < count($basket); $left++) { for ($right = $left + 1; $right < count($basket); $right++) { $key = $basket[$left].':'.$basket[$right]; $pairs[$key] = ($pairs[$key] ?? 0) + 1; } } }
            foreach ($pairs as $key => $supportCount) { [$left, $right] = array_map('intval', explode(':', $key)); $support = $supportCount / $transactionCount; foreach ([[$left, $right], [$right, $left]] as [$antecedent, $consequent]) { $confidence = $supportCount / $singles[$antecedent]; $lift = $confidence / ($singles[$consequent] / $transactionCount); if ($support < $minimumSupport || $confidence < $minimumConfidence || $lift < $minimumLift) continue; $rule = AprioriRule::query()->create(['analysis_run_id' => $run->id, 'support' => $support, 'confidence' => $confidence, 'lift' => $lift, 'support_count' => $supportCount]); $rule->items()->createMany([['additional_item_id' => $antecedent, 'side' => 'antecedent'], ['additional_item_id' => $consequent, 'side' => 'consequent']]); } }
            $run->update(['rule_count' => $run->rules()->count()]);
            return $run;
        });
    }
}
