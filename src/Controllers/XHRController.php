<?php


namespace PlaPok\Controllers;


use EasyMysql\Exceptions\EasyMysqlQueryException;
use PlaPok\Controllers\Globals\Variable;
use PlaPok\Enum\ParticipantStatus;
use PlaPok\Enum\StoryPoint;
use PlaPok\Exceptions\ParticipantNotFound;
use PlaPok\Exceptions\RoomInfoNotFound;
use PlaPok\Exceptions\RoomNotFound;
use PlaPok\Services\RoomService;
use PlaPok\Services\ViewService;

class XHRController
{
    /** @var RoomService */
    private RoomService $roomService;
    /** @var Variable */
    private Variable $variable;
    /** @var ViewService */
    private ViewService $viewService;

    public function __construct(RoomService $roomService, Variable $variable, ViewService $viewService)
    {
        $this->roomService = $roomService;
        $this->variable = $variable;
        $this->viewService = $viewService;
    }

    /**
     * @throws RoomInfoNotFound
     */
    public function xhrRoomInfo(): void
    {
        [$roomKey] = $this->roomService->roomInfo();
        $roomInfo = $this->roomService->roomData($roomKey);
        $this->viewService->print($roomInfo->jsonSerialize());
    }

    /**
     * @throws RoomInfoNotFound
     * @throws EasyMysqlQueryException
     * @throws ParticipantNotFound
     * @throws RoomNotFound
     */
    public function xhrParticipantReady(): void
    {
        [$roomKey, $username] = $this->roomService->roomInfo();
        $this->roomService->setParticipantStatus($roomKey, $username, ParticipantStatus::READY());
        $this->viewService->print('{}');
    }

    /**
     * @throws RoomInfoNotFound
     * @throws EasyMysqlQueryException
     * @throws ParticipantNotFound
     * @throws RoomNotFound
     */
    public function xhrSendStoryPoint(): void
    {
        $sp = $this->variable->post('story_point');
        if (empty($sp)) {
            throw new \RuntimeException('Invalid story point');
        }
        [$roomKey, $username] = $this->roomService->roomInfo();
        $this->roomService->setStoryPoint($roomKey, $username, new StoryPoint($sp));
        $this->viewService->print('{}');
    }

    /**
     * @throws EasyMysqlQueryException
     * @throws RoomInfoNotFound
     * @throws RoomNotFound
     */
    public function xhrStartResetRoom(): void
    {
        [$roomKey] = $this->roomService->roomInfo();
        $this->roomService->startResetRoom($roomKey);
    }

    /**
     * @throws EasyMysqlQueryException
     * @throws RoomInfoNotFound
     * @throws RoomNotFound
     */
    public function xhrAckReset(): void
    {
        [$roomKey, $name, $participantId] = $this->roomService->roomInfo();
        $this->roomService->ackReset($roomKey, $participantId);
    }

    public function xhrFinishResetRoom(): void
    {
        [$roomKey] = $this->roomService->roomInfo();
        $this->roomService->finishResetRoom($roomKey);
    }



}
