<?php


namespace PlaPok\Entities;


use JsonException;
use PlaPok\Enum\ParticipantStatus;
use PlaPok\Enum\RoomStatus;
use RuntimeException;

class RoomInfo
{
    public const ID_PREFIX = 'participant_';

    private RoomStatus $roomStatus;

    /** @var Participant[] */
    private array $participants;

    public function __construct(bool $isResetting, Participant ...$participants)
    {
        $this->participants = $participants;

        if ($isResetting) {
            $this->roomStatus = RoomStatus::IS_RESETTING();
        }else {
            $allReady = true;
            $allHaveNumbers = true;
            foreach ($participants as $participant) {
                if ($participant->getParticipantStatus()->equals(ParticipantStatus::NOT_READY())) {
                    $allReady = false;
                }
                if ($participant->getStoryPoint() === null) {
                    $allHaveNumbers = false;
                }
            }

            if ($allHaveNumbers && $allReady) {
                $this->roomStatus = RoomStatus::FINISHED();
            } elseif ($allReady) {
                $this->roomStatus = RoomStatus::ALL_READY();
            } else {
                $this->roomStatus = RoomStatus::NOT_ALL_READY();
            }
        }
    }

    /**
     * @return RoomStatus
     */
    public function getRoomStatus(): RoomStatus
    {
        return $this->roomStatus;
    }

    /**
     * @return Participant[]
     */
    public function getParticipants(): array
    {
        return $this->participants;
    }

    public function jsonSerialize(): string
    {
        $allParticipants = [];
        foreach ($this->getParticipants() as $participant) {
            $allParticipants[] = [
                'id' => self::ID_PREFIX . $participant->getId(),
                'name' => $participant->getName(),
                'isReady' => $participant->getParticipantStatus()->getValue(),
                'number' => $participant->getStoryPoint()?->getValue(),
                'ackReset' => $participant->getAckReset()
            ];
        }

        try {
            return json_encode([
                'room_status' => $this->getRoomStatus()->getValue(),
                'participants' => $allParticipants
            ], JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new RuntimeException('Cannot json encode room data', 0, $e);
        }
    }

}
