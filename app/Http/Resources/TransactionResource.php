<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'               => $this->id,
            'type'             => $this->type,
            'amount'           => $this->amount,
            'amount_formatted' => $this->formatted_amount,
            'description'      => $this->description,
            'created_by'       => $this->creator ? $this->creator->email : 'System',
            'created_at'       => $this->created_at->toDateTimeString(),
        ];
    }
}
