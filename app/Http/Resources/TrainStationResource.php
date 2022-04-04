<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TrainStationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     */
    public function toArray($request): array {
        return [
            "id"            => (int) $this->id,
            "name"          => $this->name,
            "latitude"      => $this->latitude,
            "longitude"     => $this->longitude,
            "ibnr"          => (int) $this->ibnr,
            "rilIdentifier" => $this->rilIdentifier
        ];
    }
}
