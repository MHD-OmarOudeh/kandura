<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletTransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'type_label' => $this->getTypeLabel(),
            'amount' => (float) $this->amount,
            'balance_before' => (float) $this->balance_before,
            'balance_after' => (float) $this->balance_after,
            'description' => $this->description,

            // Related data
            'order' => $this->when($this->order, [
                'id' => $this->order?->id,
                'order_number' => $this->order?->order_number,
            ]),

            'performed_by' => $this->when($this->performedBy, [
                'id' => $this->performedBy?->id,
                'name' => $this->performedBy?->name,
            ]),

            // Flags
            'is_credit' => $this->isCredit(),
            'is_debit' => $this->isDebit(),

            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
