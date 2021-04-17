<?php


namespace PlaPok\Entities;


use PlaPok\Enum\ParticipantStatus;
use PlaPok\Enum\StoryPoint;

class Participant
{
    private string $name;
    private ParticipantStatus $participantStatus;
    private ?StoryPoint $storyPoint;
    private $id;
    private ?bool $ackReset;

    public function __construct($id, string $name, ParticipantStatus $participantStatus, ?StoryPoint $number, bool $ackReset)
    {
        $this->id = $id;
        $this->name = $name;
        $this->participantStatus = $participantStatus;
        $this->storyPoint = $number;
        $this->ackReset = $ackReset;
    }

    public function getAckReset(): bool
    {
        return $this->ackReset;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getParticipantStatus(): ParticipantStatus
    {
        return $this->participantStatus;
    }

    public function getStoryPoint(): ?StoryPoint
    {
        return $this->storyPoint;
    }


}
