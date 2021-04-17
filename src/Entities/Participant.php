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

    public function __construct($id, string $name, ParticipantStatus $participantStatus, ?StoryPoint $number)
    {
        $this->id = $id;
        $this->name = $name;
        $this->participantStatus = $participantStatus;
        $this->storyPoint = $number;
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
