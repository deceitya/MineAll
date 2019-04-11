<?php

namespace deceitya\mineall;

use pocketmine\event\Listener;
use pocketmine\event\block\BlockBreakEvent;

use pocketmine\block\Block;

/**
 * イベントリスナー
 *
 * @author deceitya
 */
class EventListener implements Listener
{
    private $blocks = [];
    private $flags = [];

    public function __construct(array $blocks)
    {
        $this->blocks = $blocks;
    }

    /**
     * @priority HIGHEST
     * @ignoreCancelled
     */
    public function onBlockBreak(BlockBreakEvent $event)
    {
        if (!in_array($event->getBlock()->getId(), $this->blocks, true)) {
            return;
        }

        $player = $event->getPlayer();
        $name = $player->getName();

        if (!isset($this->flags[$name]) || !$this->flags[$name]) {
            $event->setCancelled(true);

            $this->flags[$name] = true;

            $block = $event->getBlock();
            $item = $player->getInventory()->getItemInHand();
            $vector = null;

            foreach ($this->getNeighbors($block, [$block]) as $neighbor) {
                $vector = $neighbor->asVector3();
                $player->level->useBreakOn($vector, $item, $player, true);
            }

            $this->flags[$name] = false;
        }
    }

    /**
     * 隣り合った同じIDのブロックを取得する
     *
     * @param Block $block 中心のブロック
     * @param array $list  探索済みのリスト
     *
     * @return array
     */
    private function getNeighbors(Block $block, array $list = []): array
    {
        foreach ($block->getAllSides() as $side) {
            if ($block->getId() !== $side->getId()) {
                continue;
            }
            if (in_array($side, $list, true)) {
                continue;
            }

            $list[] = $side;

            $list = $this->getNeighbors($side, $list);
        }

        return $list;
    }
}
