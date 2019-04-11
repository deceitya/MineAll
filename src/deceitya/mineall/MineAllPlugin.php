<?php

namespace deceitya\mineall;

use pocketmine\plugin\PluginBase;

/**
 * プラグインクラス
 *
 * @author deceitya
 */
class MineAllPlugin extends PluginBase
{
    public function onEnable()
    {
        $this->saveDefaultConfig();

        $this->getServer()->getPluginManager()->registerEvents(
            new EventListener($this->getConfig()->get("blocks")),
            $this
        );
    }
}
