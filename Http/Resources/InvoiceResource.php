<?php
namespace Ant\Payment\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'billed_to' => $this->billed_to,
            'items' => $this->items,
            'issue_date' => $this->issue_date,
            'display_attendee' => $this->display_attendee,
        ];
    }
}