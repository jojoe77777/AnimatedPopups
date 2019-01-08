<?php

namespace AnimatedPopups;

use pocketmine\Player;
use pocketmine\scheduler\PluginTask;

class MessageTask extends PluginTask {

    const LOOP = 0; // When finished goes back to frame 1
    const REVERSE_LOOP, PING_PONG = 1; // Reverses when reaches the end, goes back and forth

    const REVERSING = 0;
    const FORWARD = 1;

    public $owner;
    public $config;
    public $frame;
    public $stage;

    public function __construct($owner, $config){
        $this->owner = $owner;
        $this->config = $config;
        $this->frame = 0;
        $this->stage = 0;
        parent::__construct($owner);
    }

    public function onRun($currentTick){
        $frames = $this->config["text"];
        if($this->config["random"]){
            $this->broadcastPopup($frames[mt_rand(0, count($frames) - 1)]);
            return;
        }
        if($this->stage === self::REVERSING){
            if($this->frame === -1){
                $this->stage = self::FORWARD;
                $this->frame = 1;
                $this->broadcastPopup($frame = $frames[$this->frame]);
            }
        }
        if($this->frame > array_reverse(array_keys($frames))[0]){
            if($this->config["animation-type"] === self::REVERSE_LOOP){
                $this->frame -= 2;
                $this->stage = self::REVERSING;
                return;
            }
            $this->frame = 0;
            return;
        }
        if($this->stage === self::REVERSING){
            $this->broadcastPopup($frames[$this->frame]);
            $this->frame--;
            return;
        }
        $this->broadcastPopup($frames[$this->frame]);
        $this->frame++;
        return;
    }

    public function broadcastPopup($string){
        foreach($this->owner->getServer()->getOnlinePlayers() as $player) {
            $player->sendPopup($this->applyPlaceholders($string, $player) . str_repeat("\n", $this->config["offset-lines"]));
        }
    }

    public function applyPlaceholders($string, Player $player){
        $placeholders = [
            "{x}" => $player->getX(),
            "{y}" => $player->getY(),
            "{z}" => $player->getZ(),
            "{name}" => $player->getName(),
            "{displayname}" => $player->getDisplayName(),
            "{gamemode}" => $player->getGamemode(),
            "{health}" => $player->getHealth(),
            "{ip}" => $player->getAddress(),
            "{nametag}" => $player->getNameTag(),
            "{yaw}" => $player->getYaw(),
            "{pitch}" => $player->getPitch(),
            "{world}" => $player->getLevel()->getName(),
            "{line}" => "\n"
        ];
        return str_ireplace(array_keys($placeholders), array_values($placeholders), $string);
    }

}
