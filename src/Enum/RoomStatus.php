<?php


namespace PlaPok\Enum;

use MyCLabs\Enum\Enum;

/**
 * Class RoomStatus
 * @package PlaPok\Enum
 *
 * @method static RoomStatus NOT_ALL_READY()
 * @method static RoomStatus ALL_READY()
 * @method static RoomStatus FINISHED()
 * @method static RoomStatus IS_RESETTING()
 */
class RoomStatus extends Enum
{
    private const NOT_ALL_READY = 1;
    private const ALL_READY = 2;
    private const FINISHED = 3;
    private const IS_RESETTING = 4;
}
