<?php

namespace App\Models;

use App\Core\Model;

class Mission extends Model
{
    protected ?int $id = null;
    protected ?int $drones;
    protected ?int $checkpoints;
    protected ?string $type;
    protected ?float $evaluation;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDrones(): ?int
    {
        return $this->drones;
    }

    public function setDrones(?int $drones): void
    {
        $this->drones = $drones;
    }

    public function getCheckpoints(): ?int
    {
        return $this->checkpoints;
    }

    public function setCheckpoints(?int $checkpoints): void
    {
        $this->checkpoints = $checkpoints;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    public function getEvaluation(): ?float
    {
        return $this->evaluation;
    }

    public function setEvaluation(?float $evaluation): void
    {
        $this->evaluation = $evaluation;
    }
}