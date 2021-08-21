<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
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
            'comment' => $this->comment,
            'status' => $this->status,
            'property_id' => $this->property_id,
            'employee' =>$this->employee,
            'company' =>$this->company,
            'property' =>$this->property,
            'user' =>  $this->user,
        ];
    }
}
