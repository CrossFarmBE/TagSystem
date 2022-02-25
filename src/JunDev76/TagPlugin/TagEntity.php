<?php

namespace JunDev76\TagPlugin;

use pocketmine\entity\Human;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\nbt\tag\CompoundTag;

class TagEntity extends Human{

    public $gravity = 0;
    public string $tagtext;

    protected function initEntity(CompoundTag $nbt) : void{
        parent::initEntity($nbt);

        $this->setImmobile(false);
        $this->setNameTagAlwaysVisible();

        $this->setScale(0.01);

        $this->setNameTag(($this->tagtext = $nbt->getString('tagtext', '<불러오기 실패>')));
    }

    public function saveNBT() : CompoundTag{
        $nbt = parent::saveNBT();
        if(isset($this->tagtext)){
            $nbt->setString('tagtext', $this->tagtext);
        }
        return $nbt;
    }

    public function applyDamageModifiers(EntityDamageEvent $source) : void{
        $source->cancel();
    }

}