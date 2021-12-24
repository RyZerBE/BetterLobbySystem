<?php

namespace baubolp\ryzerbe\lobbycore\holo;

use baubolp\ryzerbe\lobbycore\Loader;
use mysqli;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\entity\Skin;
use pocketmine\level\ChunkLoader;
use pocketmine\level\format\Chunk;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use ryzerbe\core\util\async\AsyncExecutor;
use ryzerbe\core\util\skin\SkinDatabase;
use ryzerbe\core\util\SkinUtils;
use ryzerbe\core\util\time\TimeAPI;
use ryzerbe\core\util\Vector3Utils;
use function array_keys;
use function array_search;
use function array_walk;
use function count;
use function implode;
use function in_array;
use function spl_object_id;
use function uniqid;

class GameTimeLeaderboard extends Human implements ChunkLoader {

    public ?array $top = null;

    public function __construct(public Position $pos, public bool $team = true, public int $limit = 5){
        Loader::$leaderboards[Vector3Utils::toString($this->pos->asVector3())] = $this;
        $this->skin = new Skin(uniqid(), SkinUtils::fromImage("/root/RyzerCloud/data/NPC/backup_skin.png"), "", SkinUtils::DEFAULT_GEOMETRY_NAME, SkinUtils::DEFAULT_GEOMETRY);
        $pos->getLevelNonNull()->loadChunk($pos->x >> 4, $pos->z >> 4);
        $pos->getLevelNonNull()->registerChunkLoader($this, $pos->x >> 4, $pos->z >> 4, true);
        parent::__construct($pos->getLevelNonNull(), Entity::createBaseNBT($pos->asVector3()));
    }

    public function onUpdate(int $currentTick): bool{
        return false;
    }

    public function entityBaseTick(int $tickDiff = 1): bool{
        return false;
    }

    public function load(bool $spawn = true){
        $leaderboard = $this;
        $limit = $this->limit;
        $team = $this->team;
        AsyncExecutor::submitMySQLAsyncTask("RyZerCore", function(mysqli $mysqli) use ($limit, $team): array{
            $top = [];
            if($team) {
                $res = $mysqli->query("SELECT * FROM `gametime` ORDER BY ticks DESC LIMIT $limit");
                if($res->num_rows <= 0) return [];

                while($data = $res->fetch_assoc()) {
                    $top[$data["player"]] = TimeAPI::convert($data["ticks"] ?? 0)->asShortString();
                }
            }else {
                $res = $mysqli->query("SELECT * FROM `gametime` ORDER BY ticks DESC LIMIT 50");
                if($res->num_rows <= 0) return [];

                while($data = $res->fetch_assoc()) {
                    $top[$data["player"]] = $data["ticks"] ?? 0;
                }

                $tops = $top;
                $top = [];
                foreach($tops as $playerName => $ticks) {
                    $res = $mysqli->query("SELECT rankname FROM playerranks WHERE player='$playerName'");
                    if($res->num_rows <= 0){
                        $top[$playerName] = TimeAPI::convert($ticks)->asShortString();
                        if(count($top) >= $limit) return $top;
                        continue;
                    }
                    if($data = $res->fetch_assoc()) {
                        $ranks = ["Admin", "Content", "Architekt", "Builder", "Designer", "Developer", "Management", "Manager", "Staff", "Guardian"];

                        if(!in_array($data["rankname"], $ranks)){
                            $top[$playerName] = TimeAPI::convert($ticks)->asShortString();
                        }
                        if(count($top) >= $limit) return $top;
                    }
                }
            }
            for($i = count($top); $i < 5; $i++) {
                $top["???"] = "/";
            }
            return $top;
        }, function(Server $server, array $top) use ($spawn, $leaderboard, $team): void{
            $leaderboard->top = $top;
            array_walk($top, function(&$v, $k) use ($top): void{
                $v = TextFormat::GOLD.(array_search($k, array_keys($top)) +1).". ".TextFormat::AQUA.$k.TextFormat::DARK_GRAY." » ".TextFormat::GREEN.$v;
            });
            $leaderboard->setNameTag(TextFormat::GOLD."GameTime Leaderboard ".(($team === true) ? "" : TextFormat::DARK_GRAY."(".TextFormat::RED."Without team".TextFormat::DARK_GRAY.")")."\n".implode("\n", $top));
            $leaderboard->setNameTagAlwaysVisible();
            if($spawn) {
                $leaderboard->spawnToAll();
                $leaderboard->setScale(0.00000001);
            }
        });
    }

    /**
     * @return int
     */
    public function getLoaderId(): int{
        return spl_object_id($this);
    }

    /**
     * @return bool
     */
    public function isLoaderActive(): bool{
        return !$this->isClosed();
    }

    /**
     * @return bool
     */
    public function isDespawned(): bool{
        return Loader::getInstance()->getServer()->getDefaultLevel()->getEntity($this->getEntityId()) === null;
    }

    public function onChunkChanged(Chunk $chunk){
    }

    public function onChunkLoaded(Chunk $chunk){
    }

    public function onChunkUnloaded(Chunk $chunk){
    }

    public function onChunkPopulated(Chunk $chunk){
    }

    public function onBlockChanged(Vector3 $block){
    }

    public function getPosition(): Position{
        return $this->pos;
    }

    public function getLevel(): Level{
        return $this->pos->getLevelNonNull();
    }
}