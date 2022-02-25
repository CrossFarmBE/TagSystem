<?php

/*
       _             _____           ______ __
      | |           |  __ \         |____  / /
      | |_   _ _ __ | |  | | _____   __ / / /_
  _   | | | | | '_ \| |  | |/ _ \ \ / // / '_ \
 | |__| | |_| | | | | |__| |  __/\ V // /| (_) |
  \____/ \__,_|_| |_|_____/ \___| \_//_/  \___/


This program was produced by JunDev76 and cannot be reproduced, distributed or used without permission.

Developers:
 - JunDev76 (https://github.jundev.me/)

Copyright 2022. JunDev76. Allrights reserved.
*/

namespace JunDev76\TagPlugin;

use Exception;
use FormSystem\form\ModalForm;
use JunKR\CrossUtils;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\entity\Human;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\world\World;

class TagPlugin extends PluginBase{

    /**
     * @throws Exception
     */
    public function onEnable() : void{
        CrossUtils::registercommand('태그생성', $this, '', DefaultPermissions::ROOT_OPERATOR);
        CrossUtils::registercommand('태그삭제', $this, '', DefaultPermissions::ROOT_OPERATOR);

        EntityFactory::getInstance()->register(TagEntity::class, static function(World $world, CompoundTag $nbt): TagEntity{
            return new TagEntity(EntityDataHelper::parseLocation($nbt, $world), Human::parseSkinNBT($nbt), $nbt);
        }, ['TagEntity']);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
        if(!$sender instanceof Player){
            return true;
        }
        if($command->getName() === '태그생성'){
            if(!isset($args[0])){
                $sender->sendMessage('비어있음');
                return true;
            }

            $message = implode(' ', $args);
            $message = str_replace('{줄바꿈}', PHP_EOL, $message);

            $nbt = CompoundTag::create()->setString('tagtext', $message);
            $entity = new TagEntity($sender->getLocation(), $sender->getSkin(), $nbt);
            $entity->spawnToAll();
            return true;
        }
        if($command->getName() === '태그삭제'){
            $entity = $sender->getWorld()->getNearestEntity($sender->getPosition(), 5, TagEntity::class);
            if($entity === null){
                $sender->sendMessage('찾을 수 없음');
                return true;
            }

            $form = new ModalForm(function(Player $player, $data) use ($entity){
                if($data === true){
                    $entity->close();
                }
            });
            $form->setTitle('태그삭제');
            $form->setContent('태그:' . PHP_EOL . PHP_EOL . $entity->getNameTag() . PHP_EOL . PHP_EOL . '§r§f을 삭제할까요?');
            $form->setButton1('§l삭제');
            $form->setButton2('삭제X');
            $form->sendForm($sender);
        }
        return true;
    }

}