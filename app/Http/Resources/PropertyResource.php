<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PropertyResource extends JsonResource
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
            'id'=>$this->id,
            'company' => $this->company,
            'employee' => $this->employee,
            'reservation' =>$this->Reservation,
            'title' => $this->title,
            'price' => $this->price,
            'price_sqft' => $this->price_sqft,
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
            'unit_type'=>$this->unit_type,
            'unit_area'=>$this->unit_area,
            'is_corner'=>$this->is_corner,
            'is_for_rent'=>$this->is_for_rent,
            'is_flat'=>$this->is_flat,
            'is_constructed'=>$this->is_constructed,
            'is_balted'=>$this->is_balted,
            'is_installment_available'=>$this->is_installment_available,
            'kitchen'=>$this->kitchen,
            'bedrooms'=>$this->bedrooms,
            'bathrooms'=>$this->bathrooms,
            'gerage'=>$this->gerage,
            'total_rooms'=>$this->total_rooms,
            'total_floors'=>$this->total_floors,
            'status'=>$this->status,
            'is_available'=>$this->is_available,
            'is_verified'=>$this->is_verified,
            'society'=>$this->society,
            'phase'=>$this->phase,
            'block'=>$this->block,
            'address'=>$this->address,
            'zip_code'=>$this->zip_code,
            'city'=>$this->city,
            'state'=>$this->state,
            'country'=>$this->country,
            'description'=>$this->description,
            'building_year'=>$this->building_year,
            'images' =>$this->images,
            'category' =>$this->categories,
        ];
    }
}
