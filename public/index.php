<?php
error_reporting(E_ALL);
ini_set('display_errors','On');

use EasyMysql\Config;
use EasyMysql\Enum\MysqlDriverEnum;
use PlaPok\Controllers\WebController;
use PlaPok\Controllers\XHRController;

session_start();
require_once '../defines.php';
require_once '../vendor/autoload.php';

$builder = new \DI\ContainerBuilder();

$builder->addDefinitions([
    Config::class => DI\create()->constructor(
        MysqlDriverEnum::PDO(),
        DB_HOST,
        DB_USER,
        DB_PASS,
        DB_NAME,
        DB_PORT
    )
]);
$container = $builder->build();

$action = $_GET['a'] ?? null;
try {
    switch ($action) {
        case 'index':
        case null:
            $container->get(WebController::class)->index();
            break;
        case 'createRoom':
            $container->get(WebController::class)->createRoom();
            break;
        case 'joinRoom':
            $container->get(WebController::class)->joinRoom();
            break;
        case 'joined':
            $container->get(WebController::class)->joined();
            break;
        case 'xhrRoomInfo':
            $container->get(XHRController::class)->xhrRoomInfo();
            break;
        case 'xhrParticipantReady':
            $container->get(XHRController::class)->xhrParticipantReady();
            break;
        case 'xhrSendStoryPoint':
            $container->get(XHRController::class)->xhrSendStoryPoint();
            break;
        case 'xhrStartResetRoom':
            $container->get(XHRController::class)->xhrStartResetRoom();
            break;
        case 'xhrAckReset':
            $container->get(XHRController::class)->xhrAckReset();
            break;
        case 'xhrFinishResetRoom':
            $container->get(XHRController::class)->xhrFinishResetRoom();
            break;
        case 'exitRoom':
            $container->get(WebController::class)->exitRoom();
            break;
        case 'xhrKickParticipant':
            $container->get(XHRController::class)->xhrKickParticipant();
            break;
        case 'youHaveBeenKicked':
            $container->get(WebController::class)->youHaveBeenKicked();
            break;
        default:
            throw new RuntimeException('Invalid action');
            break;
    }
} catch (\EasyMysql\Exceptions\EasyMysqlQueryException $e) {
    http_response_code(500);
    echo '<pre>';
    echo $e->getMessage();
    echo "\n";
    echo $e->getQuery();
    echo "\n";
    echo print_r($e->getBinds(), 1);
    echo '</pre>';
}
 catch (\Exception $e) {
    http_response_code(500);
    echo '<pre>';
    echo $e->getMessage();
    echo '</pre>';
}
