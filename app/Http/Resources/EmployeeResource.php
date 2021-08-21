<?php
namespace App\Http\Resources;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
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
            'type' => $this->type,
            'photo' => $this->photo,
            'working_hours' => $this->working_hours,
            'joining_salary' => $this->joining_salary,
            'joining_date' => $this->joining_date,
            'current_salary' => $this->current_salary,
            'user' => $this->user,
            'company'=>$this->company,
            'properties' => $this->properties,
        ];
    }
}
