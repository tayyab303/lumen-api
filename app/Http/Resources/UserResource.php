<?php
namespace App\Http\Resources;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'username' => $this->username,
            'email' => $this->email,
            'age' => $this->age,
            'gender' => $this->gender,
            'city' => $this->city,
            'state' => $this->state,
            'coutry' => $this->coutry,
            'zip_code' => $this->zip_code,
            'address' => $this->address,
            'ph' => $this->ph,
            'cnic' => $this->cnic,     
        ];
    }
}
