<?php

namespace App\DataTransferObjects\Hafas;

use App\Models\TrainStation;
use Illuminate\Support\Collection;

class StationboardDeparture
{
    public string $tripId;

    public ?string $when;        // Format is 8601, ie: "2022-09-29T20:24:00+02:00"
    public ?string $plannedWhen; // Format is 8601, ie: "2022-09-29T20:24:00+02:00"
    public int     $delay;       // in seconds

    public ?string $platform;
    public ?string $plannedPlatform;

    public string $direction;

    public array $remarks;


    public $stop;
    public $line;
    public $origin;
    public $destination;

    public TrainStation $station;

    public function __construct($dataItem) {
        $this->tripId          = $dataItem->tripId;
        $this->stop            = $dataItem->stop;
        $this->when            = $dataItem->when;
        $this->plannedWhen     = $dataItem->plannedWhen;
        $this->delay           = $dataItem->delay ?? 0;
        $this->platform        = $dataItem->platform;
        $this->plannedPlatform = $dataItem->plannedPlatform;
        $this->direction       = $dataItem->direction;
        $this->line            = $dataItem->line;
        $this->remarks         = $dataItem->remarks;
        $this->origin          = $dataItem->origin;
        $this->destination     = $dataItem->destination;
    }

    public function sortKey(): ?string {
        return $this->when ?? $this->plannedWhen;
    }

    public function getTrainStationModel(): array {
        return [
            'ibnr'      => $this->stop->id,
            'name'      => $this->stop->name,
            'latitude'  => $this->stop?->location?->latitude,
            'longitude' => $this->stop?->location?->longitude,
        ];
    }

    public function setTrainStationModelFromSet(Collection $trainStations): void {
        $this->station = $trainStations
            ->where('ibnr', $this->stop->id)
            ->first();
    }
}
