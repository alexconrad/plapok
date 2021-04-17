<?php


namespace PlaPok\Enum;


use MyCLabs\Enum\Enum;

/**
 * Class StoryPoint
 * @package PlaPok\Enum
 *
 * @method static StoryPoint ONE()
 * @method static StoryPoint TWO()
 * @method static StoryPoint THREE()
 * @method static StoryPoint FIVE()
 * @method static StoryPoint EIGHT()
 * @method static StoryPoint DONT_KNOW()
 */

class StoryPoint extends Enum
{
    private const ONE = '1';
    private const TWO = '2';
    private const THREE = '3';
    private const FIVE = '5';
    private const EIGHT = '8';
    private const DONT_KNOW = '?';
}
