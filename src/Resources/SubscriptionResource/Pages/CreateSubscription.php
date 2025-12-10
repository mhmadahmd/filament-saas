<?php

namespace Mhmadahmd\FilamentSaas\Resources\SubscriptionResource\Pages;

use Carbon\Carbon;
use Filament\Resources\Pages\CreateRecord;
use Mhmadahmd\FilamentSaas\Models\Plan;
use Mhmadahmd\FilamentSaas\Models\SubscriptionPayment;
use Mhmadahmd\FilamentSaas\Resources\SubscriptionResource;

class CreateSubscription extends CreateRecord
{
    protected static string $resource = SubscriptionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $paymentData = [
            'payment_method' => $data['payment_method'] ?? null,
            'payment_status' => $data['payment_status'] ?? SubscriptionPayment::STATUS_PENDING,
            'payment_amount' => $data['payment_amount'] ?? null,
            'payment_currency' => $data['payment_currency'] ?? 'USD',
            'transaction_id' => $data['transaction_id'] ?? null,
            'reference_number' => $data['reference_number'] ?? null,
            'payment_notes' => $data['payment_notes'] ?? null,
        ];

        $this->paymentData = $paymentData;
        
        $name = $data['name'] ?? 'main';
        if (is_array($name)) {
            $name = ! empty($name) ? (string) reset($name) : 'main';
        }
        $name = (string) $name;
        
        $description = $data['description'] ?? null;
        if (is_array($description) && ! empty($description)) {
            $description = $description;
        } elseif (is_array($description) && empty($description)) {
            $description = null;
        }
        
        $this->subscriptionData = [
            'name' => $name,
            'description' => $description,
            'subscriber_type' => $data['subscriber_type'],
            'subscriber_id' => $data['subscriber_id'],
            'plan_id' => $data['plan_id'],
            'starts_at' => isset($data['starts_at']) ? Carbon::parse($data['starts_at']) : null,
        ];

        return $data;
    }

    protected array $paymentData = [];
    protected array $subscriptionData = [];

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $subscriberType = $this->subscriptionData['subscriber_type'];
        $subscriberId = $this->subscriptionData['subscriber_id'];
        $planId = $this->subscriptionData['plan_id'];

        $subscriber = $subscriberType::findOrFail($subscriberId);
        $plan = Plan::findOrFail($planId);

        $subscriptionName = $this->subscriptionData['name'] ?? 'main';
        $startDate = $this->subscriptionData['starts_at'];

        $subscription = $subscriber->newPlanSubscription($subscriptionName, $plan, $startDate);

        if (! empty($this->subscriptionData['description'])) {
            $subscription->description = $this->subscriptionData['description'];
            $subscription->save();
        }

        return $subscription;
    }

    protected function afterCreate(): void
    {
        if (! empty($this->paymentData['payment_method'])) {
            $subscription = $this->record;
            $plan = $subscription->plan;

            $amount = $this->paymentData['payment_amount'] ?? ($plan->price + $plan->signup_fee);
            $currency = $this->paymentData['payment_currency'] ?? $plan->currency;

            $payment = SubscriptionPayment::create([
                'subscription_id' => $subscription->id,
                'payment_method' => $this->paymentData['payment_method'],
                'amount' => $amount,
                'currency' => $currency,
                'status' => $this->paymentData['payment_status'],
                'transaction_id' => $this->paymentData['transaction_id'],
                'reference_number' => $this->paymentData['reference_number'],
                'notes' => $this->paymentData['payment_notes'],
                'paid_at' => $this->paymentData['payment_status'] === SubscriptionPayment::STATUS_PAID ? now() : null,
            ]);
        }
    }
}
