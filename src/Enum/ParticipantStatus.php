<?php
/** @noinspection PhpUnusedPrivateFieldInspection */
declare(strict_types=1);

namespace PlaPok\Enum;


use MyCLabs\Enum\Enum;

/**
 * Class ParticipantStatus
 * @package PlaPok\Enum
 *
 * @method static ParticipantStatus NOT_READY()
 * @method static ParticipantStatus READY()
 */
class ParticipantStatus extends Enum
{
    private const NOT_READY = 0;
    private const READY = 1;

}
