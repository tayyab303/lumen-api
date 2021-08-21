<?php
namespace App\Http\Resources;

use Illuminate\Support\Facades\Crypt;
use App\Http\Resources\PropertyResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
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
            'name' => $this->name,
            'logo' => $this->logo,
            'email' => $this->email,
            'ph' => $this->ph,
            'fax' => $this->fax,
            'location' => $this->location,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'country' => $this->country,
            'zip_code' => $this->zip_code,
            'about'=> $this->about,
            'properties' => $this->properties,
            'employees' =>$this->employees,
            'user' =>  $this->user
        ];
    }
}
