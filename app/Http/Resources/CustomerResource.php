<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    /**
    * Transform the resource into an array.
    *
    * @param \Illuminate\Http\Request $request
    * @return array
    */
    public function toArray($request)
    {
        return [
            'name' => $this->name,
            'photo' => $this->photo,
            'bank_name' => $this->bank_name,
            'account_title' => $this->account_title,
            'account_no' => $this->account_no,
            'iban' => $this->iban,
            'is_overseas' => $this->is_overseas,
            'is_verified' => $this->is_verified,
            'user' => $this->user,
            'properties' => $this->properties,
        ];
    }
}
