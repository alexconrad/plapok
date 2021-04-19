<?php
declare(strict_types=1);

namespace PlaPok\Controllers;

use EasyMysql\Exceptions\EasyMysqlQueryException;
use PlaPok\Common\Common;
use PlaPok\Controllers\Globals\Variable;
use PlaPok\Exceptions\RoomInfoNotFound;
use PlaPok\Exceptions\RoomNotFound;
use PlaPok\Services\RoomService;
use PlaPok\Services\ViewService;
use RuntimeException;

class WebController
{
    private RoomService $roomService;
    private Variable $variable;
    private ViewService $viewService;

    public function __construct(RoomService $roomService, Variable $variable, ViewService $viewService)
    {
        $this->roomService = $roomService;
        $this->variable = $variable;
        $this->viewService = $viewService;
    }

    public function index(): void
    {
        $this->viewService->assign('youAreKicked', $this->roomService->getKickedNotification());
        $this->roomService->clearSession();
        $this->viewService->display('index.inc.php');
    }

    /**
     * @throws EasyMysqlQueryException
     * @throws RoomNotFound
     */
    public function joinRoom(): void
    {
        $name = $this->variable->post('name') ?? $this->variable->get('name');
        $roomKey = $this->variable->post('room_key') ?? $this->variable->get('room_key');
        if ((empty($name)) || (empty($roomKey))) {
            throw new RuntimeException('Bad name/key');
        }
        $this->roomService->joinRoom($roomKey, $name);
        header('Location: ' . Common::link([__CLASS__, 'joined']));
    }

    /**
     * @throws EasyMysqlQueryException
     * @throws RoomNotFound
     */
    public function createRoom(): void
    {
        [$roomId, $roomKey] = $this->roomService->createRoom();
        $this->roomService->joinRoom($roomKey, $this->variable->post('username'), $roomId);

        header('Location: ' . Common::link([__CLASS__, 'joined']));
    }

    /**
     * @throws RoomInfoNotFound
     */
    public function joined(): void
    {
        [$roomKey, $username, $participantId, $isHost] = $this->roomService->roomInfo();

        $this->viewService->assign('roomKey', $roomKey);
        $this->viewService->assign('username', $username);
        $this->viewService->assign('participantId', $participantId);
        $this->viewService->assign('isHost', $isHost);
        $this->viewService->display('joined.inc.php');
    }

    /**
     * @throws EasyMysqlQueryException
     * @throws RoomInfoNotFound
     * @throws RoomNotFound
     */
    public function exitRoom(): void
    {
        [$roomKey, $username, $participantId, $isHost] = $this->roomService->roomInfo();
        $this->roomService->exitRoom($roomKey, $participantId);
        $this->roomService->clearSession();
        header('Location: ' . Common::link([__CLASS__, 'index']));
    }

    public function youHaveBeenKicked(): void
    {
        $this->roomService->clearSession();
        $this->roomService->setKickedNotification();
        header('Location: ' . Common::link([__CLASS__, 'index']));
    }

}
