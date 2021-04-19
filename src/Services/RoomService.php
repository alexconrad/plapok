<?php


namespace PlaPok\Services;


use EasyMysql\EasyMysql;
use EasyMysql\Exceptions\EasyMysqlQueryException;
use PlaPok\Entities\Participant;
use PlaPok\Entities\RoomInfo;
use PlaPok\Enum\ParticipantStatus;
use PlaPok\Enum\StoryPoint;
use PlaPok\Exceptions\ParticipantNotFound;
use PlaPok\Exceptions\RoomInfoNotFound;
use PlaPok\Exceptions\RoomNotFound;

class RoomService
{
    private const ROOM_KEY = 'room_key';
    private const PARTICIPANT_ID = 'pid';
    private const USERNAME_KEY = 'username_key';
    private const IS_HOST = 'is_host';

    public const COOKIE_NAME = 'plapok_name';

    /** @var EasyMysql */
    private EasyMysql $easyMysql;
    /** @var SessionService */
    private SessionService $sessionService;

    public function __construct(EasyMysql $easyMysql, SessionService $sessionService)
    {
        $this->easyMysql = $easyMysql;
        $this->sessionService = $sessionService;
    }

    public function clearSession(): void
    {
        $this->sessionService->unset(self::ROOM_KEY);
        $this->sessionService->unset(self::USERNAME_KEY);
        $this->sessionService->unset(self::PARTICIPANT_ID);
        $this->sessionService->unset(self::IS_HOST);
    }

    /**
     * @return array<string, string, string, int>
     * @throws RoomInfoNotFound
     */
    public function roomInfo(): array
    {
        $roomKey = $this->sessionService->get(self::ROOM_KEY);
        $username = $this->sessionService->get(self::USERNAME_KEY);
        $participantId = $this->sessionService->get(self::PARTICIPANT_ID);
        $isHost = $this->sessionService->get(self::IS_HOST) ?? 0;

        if (empty($roomKey) || empty($username)) {
            throw new RoomInfoNotFound('Room info not found');
        }

        return [$roomKey, $username, $participantId, (int)$isHost];
    }

    /**
     * @param $roomKey
     * @param $name
     * @param null $roomId
     * @throws EasyMysqlQueryException
     * @throws RoomNotFound
     */
    public function joinRoom($roomKey, $name, $roomId = null): void
    {
        if ($roomId === null) {
            $roomId = $this->getRoomId($roomKey);
        }

        $participantId = $this->easyMysql->insert('INSERT INTO people SET room_id = :room_id, name=:name, participant_status=:sts, number=NULL', [
            'room_id' => $roomId,
            'name' => $name,
            'sts' => ParticipantStatus::NOT_READY()->getValue()
        ]);

        $this->sessionService->set(self::PARTICIPANT_ID, $participantId);
        $this->sessionService->set(self::ROOM_KEY, $roomKey);
        $this->sessionService->set(self::USERNAME_KEY, $name);
        $isHost = $this->sessionService->get(self::IS_HOST) ?? 0;
        $this->sessionService->set(self::IS_HOST, $isHost);

        setcookie(self::COOKIE_NAME, $name, [
            'expires' => time() + 60 * 60 * 24 * 30,
            'httponly' => true,
        ]);
    }

    /**
     * @return array<int, string>
     * @throws EasyMysqlQueryException
     */
    public function createRoom(): array
    {
        $roomKey = bin2hex(random_bytes(3));
        $roomId = $this->easyMysql->insert('INSERT INTO rooms SET room_key = :room_key', ['room_key' => $roomKey]);
        $this->sessionService->set(self::IS_HOST, 1);
        return [$roomId, $roomKey];
    }


    public function roomData($roomKey): RoomInfo
    {
        $roomInfo = $this->easyMysql->fetchRow('SELECT * FROM rooms WHERE room_key = :room_key', ['room_key' => $roomKey]);
        $rows = $this->easyMysql->fetchAllAssociative('SELECT * FROM people WHERE room_id = :room_id', ['room_id' => $roomInfo['room_id']]);
        $participants = [];
        foreach ($rows as $row) {
            $participants[] = new Participant(
                $row['id'],
                $row['name'],
                new ParticipantStatus((int)$row['participant_status']),
                empty($row['number']) ? null : new StoryPoint($row['number']),
                (bool)$row['ack_reset'],
            );
        }

        return new RoomInfo((bool)$roomInfo['is_resetting'], ...$participants);
    }

    /**
     * @param $roomKey
     * @param $name
     * @param ParticipantStatus $participantStatus
     * @throws EasyMysqlQueryException
     * @throws ParticipantNotFound
     * @throws RoomNotFound
     */
    public function setParticipantStatus($roomKey, $name, ParticipantStatus $participantStatus): void
    {
        $roomId = $this->getRoomId($roomKey);
        $participantId = $this->getParticipantId($roomId, $name);

        $this->easyMysql->update('UPDATE people SET participant_status = :sts, number=0 WHERE id = :id ', [
            'sts' => $participantStatus->getValue(),
            'id' => $participantId,
        ]);
    }

    /**
     * @param $roomKey
     * @param $name
     * @param StoryPoint $storyPoint
     * @throws EasyMysqlQueryException
     * @throws ParticipantNotFound
     * @throws RoomNotFound
     */
    public function setStoryPoint($roomKey, $name, StoryPoint $storyPoint): void
    {
        $roomId = $this->getRoomId($roomKey);
        $participantId = $this->getParticipantId($roomId, $name);

        $this->easyMysql->update('UPDATE people SET number = :sp WHERE id = :id', [
            'sp' => $storyPoint->getValue(),
            'id' => $participantId,
        ]);
    }

    /**
     * @param $roomKey
     * @throws EasyMysqlQueryException
     * @throws RoomNotFound
     */
    public function startResetRoom($roomKey): void
    {
        $roomId = $this->getRoomId($roomKey);
        $this->easyMysql->update('UPDATE people SET ack_reset = 0 WHERE room_id = :id ', [
            'id' => $roomId,
        ]);
        $this->easyMysql->update('UPDATE rooms SET is_resetting = :re WHERE room_id = :id ', [
            're' => 1,
            'id' => $roomId,
        ]);
    }

    /**
     * @param $roomKey
     * @throws EasyMysqlQueryException
     * @throws RoomNotFound
     */
    public function finishResetRoom($roomKey): void
    {
        $roomId = $this->getRoomId($roomKey);
        $this->easyMysql->update('UPDATE people SET participant_status = :rst, number = NULL WHERE room_id = :id ', [
            'rst' => ParticipantStatus::NOT_READY()->getValue(),
            'id' => $roomId,
        ]);
        $this->easyMysql->update('UPDATE rooms SET is_resetting = :re WHERE room_id = :id ', [
            're' => 0,
            'id' => $roomId,
        ]);
    }


    /**
     * @param $roomKey
     * @param $participantId
     * @throws EasyMysqlQueryException
     * @throws RoomNotFound
     */
    public function ackReset($roomKey, $participantId)
    {
        $roomId = $this->getRoomId($roomKey);
        $this->easyMysql->update('UPDATE people SET ack_reset = 1 WHERE room_id= :room_id AND id = :id ', [
            'room_id' => $roomId,
            'id' => $participantId,
        ]);
    }


    /**
     * @param $roomKey
     * @return int
     * @throws EasyMysqlQueryException
     * @throws RoomNotFound
     */
    private function getRoomId($roomKey): int
    {
        $roomId = $this->easyMysql->fetchOne('SELECT room_id FROM rooms WHERE room_key = :room_key', ['room_key' => $roomKey]);
        if ($roomId === null) {
            throw new RoomNotFound('Invalid room key');
        }
        return (int)$roomId;
    }


    /**
     * @param $roomId
     * @param $name
     * @return int
     * @throws EasyMysqlQueryException
     * @throws ParticipantNotFound
     */
    private function getParticipantId($roomId, $name): int
    {
        $participantId = $this->easyMysql->fetchOne('SELECT id FROM people WHERE room_id = :room_id AND name = :name', [
            'room_id' => $roomId,
            'name' => $name
        ]);

        if ($participantId === null) {
            throw new ParticipantNotFound('Participant not found');
        }

        return (int)$participantId;

    }

    /**
     * @param $roomKey
     * @param $participantId
     * @throws EasyMysqlQueryException
     * @throws RoomNotFound
     */
    public function exitRoom($roomKey, $participantId): void
    {
        $roomId = $this->getRoomId($roomKey);
        $this->easyMysql->delete('DELETE FROM people WHERE room_id = :room_id AND id = :id', [
            'room_id' => $roomId,
            'id' => $participantId
        ]);
    }

    /**
     * @param $roomKey
     * @param $participantName
     * @throws EasyMysqlQueryException
     * @throws RoomNotFound
     */
    public function deleteParticipant($roomKey, $participantName): void
    {
        $roomId = $this->getRoomId($roomKey);
        $this->easyMysql->delete('DELETE FROM people WHERE room_id = :room_id AND name = :name', [
            'room_id' => $roomId,
            'name' => $participantName
        ]);
    }


}
