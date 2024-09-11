<?php

namespace ItzSasan\FastStop;

use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\utils\TextFormat;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\utils\Config;

class Main extends PluginBase implements Listener {

    private bool $isFastStopEnabled = false;
    private array $allowedPlayers = [];
    private array $effects = [];
    private string $messageType = "title";
    private string $messageContent = "Server is updating!";
    private array $prohibitedActions = [];
    private array $effectMap;
    private int $messageCooldown = 2; // Default cooldown of 2 seconds
    private array $lastMessageTimes = [];

    public function onEnable(): void {
        // Check if settings.yml exists, if not create it with default values
        if (!file_exists($this->getDataFolder() . "settings.yml")) {
            $this->getLogger()->info("Creating default settings.yml...");
            @mkdir($this->getDataFolder());
            file_put_contents($this->getDataFolder() . "settings.yml", $this->getDefaultConfigContent());
        }

        $this->saveDefaultConfig();

        // Initialize effect map with all VanillaEffects
        $this->effectMap = [
            "NAUSEA" => VanillaEffects::NAUSEA(),
            "SLOWNESS" => VanillaEffects::SLOWNESS(),
            "WEAKNESS" => VanillaEffects::WEAKNESS(),
            "BLINDNESS" => VanillaEffects::BLINDNESS(),
            "HUNGER" => VanillaEffects::HUNGER(),
            "POISON" => VanillaEffects::POISON(),
            "REGENERATION" => VanillaEffects::REGENERATION(),
            "DAMAGE_RESISTANCE" => VanillaEffects::DAMAGE_RESISTANCE(),
            "FIRE_RESISTANCE" => VanillaEffects::FIRE_RESISTANCE(),
            "WATER_BREATHING" => VanillaEffects::WATER_BREATHING(),
            "INVISIBILITY" => VanillaEffects::INVISIBILITY(),
            "JUMP_BOOST" => VanillaEffects::JUMP_BOOST(),
            "SPEED" => VanillaEffects::SPEED(),
            "STRENGTH" => VanillaEffects::STRENGTH(),
            "LEVITATION" => VanillaEffects::LEVITATION(),
            "SLOW_FALLING" => VanillaEffects::SLOW_FALLING(),
            "BAD_OMEN" => VanillaEffects::BAD_OMEN(),
            "CONDUIT_POWER" => VanillaEffects::CONDUIT_POWER(),
            "DOLPHINS_GRACE" => VanillaEffects::DOLPHINS_GRACE(),
            "HERO_OF_THE_VILLAGE" => VanillaEffects::HERO_OF_THE_VILLAGE(),
            // Add more effects if needed
        ];

        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->info(TextFormat::GREEN . "FastStop plugin enabled!");

        // Load effects and message settings from the config
        $this->loadConfigSettings();
    }

    private function getDefaultConfigContent(): string {
        return <<<YAML
# Configuration for FastStop Plugin
# ------------------------------
# Welcome to the FastStop plugin configuration!
# For more information or to request help, please visit the GitHub repository:
# https://github.com/ItzSasan/FastStop
#
# This configuration file allows you to customize the effects applied to players, the messages displayed,
# and the actions that are prohibited when the server is in update mode.

# Effects:
# - Define the effects that should be applied to players when the server is in update mode.
# - Each effect entry must specify the effect name, duration (in ticks), and amplifier (strength).
# - The effect name should match PocketMine-MP's predefined effect names (e.g., "BLINDNESS", "SLOWNESS").
# - Duration is specified in ticks (1 second = 20 ticks).
# - Amplifier specifies the strength of the effect (0 = level 1, 1 = level 2, etc.).

effects:
  - effect: "BLINDNESS"
    duration: 800   # Duration in ticks (1 second = 20 ticks)
    amplifier: 1    # Effect strength

# Message:
# - Customize the type and content of the message displayed to players when they are affected by the update mode.
# - The message type can be "title" (full-screen title), "tip" (popup message), or "message" (standard chat message).
message:
  type: "title"
  content: "Server is updating!"

# Prohibited Actions:
# - Specify the actions that players are not allowed to perform while the server is in update mode.
# - Valid actions include "move" (movement), "interact" (interacting with blocks), "place_block" (placing blocks),
#   "break_block" (breaking blocks), "damage" (taking damage), and "damage_by_entity" (damage caused by entities).
prohibited_actions:
  - "move"
  - "interact"
  - "place_block"
  - "break_block"
  - "damage"
  - "damage_by_entity"

# Message Cooldown:
# - Duration in seconds between sending messages to prevent spamming.
message_cooldown: 2
YAML;
    }

    private function loadConfigSettings(): void {
        $config = new Config($this->getDataFolder() . "settings.yml", Config::YAML);

        // Load effects
        $this->effects = [];
        foreach ($config->get("effects", []) as $effectData) {
            $effectName = strtoupper($effectData["effect"]);
            $duration = (int) $effectData["duration"];
            $amplifier = (int) $effectData["amplifier"];

            if (isset($this->effectMap[$effectName])) {
                $effect = $this->effectMap[$effectName];
                $this->effects[] = new EffectInstance($effect, $duration, $amplifier, true, true);
            }
        }

        // Load message settings
        $this->messageType = strtolower($config->getNested("message.type", "title"));
        $this->messageContent = $config->getNested("message.content", "Server is updating!");

        // Load prohibited actions
        $this->prohibitedActions = array_map('strtolower', $config->get("prohibited_actions", []));

        // Load message cooldown
        $this->messageCooldown = (int) $config->get("message_cooldown", 2);
    }

    private function applyEffects(Player $player): void {
        foreach ($this->effects as $effect) {
            $player->getEffects()->add($effect);
        }
    }

    private function removeEffects(Player $player): void {
        foreach ($this->effects as $effect) {
            $player->getEffects()->remove($effect->getType());
        }
    }

    private function removeEffectsFromAll(): void {
        foreach ($this->getServer()->getOnlinePlayers() as $player) {
            $this->removeEffects($player);
        }
    }

    private function sendMessage(Player $player): void {
        $currentTime = microtime(true);
        $lastMessageTime = $this->lastMessageTimes[$player->getName()] ?? 0;

        if (($currentTime - $lastMessageTime) < $this->messageCooldown) {
            return;
        }

        switch ($this->messageType) {
            case "tip":
                $player->sendTip($this->messageContent);
                break;
            case "message":
                $player->sendMessage($this->messageContent);
                break;
            case "title":
            default:
                $player->sendTitle(TextFormat::RED . $this->messageContent, "", 10, 20, 10);
                break;
        }

        $this->lastMessageTimes[$player->getName()] = $currentTime;
    }

    public function onMove(PlayerMoveEvent $event): void {
        $player = $event->getPlayer();
        if ($this->isFastStopEnabled && in_array("move", $this->prohibitedActions) && !isset($this->allowedPlayers[$player->getName()])) {
            $event->cancel();
            $this->applyEffects($player);
            $this->sendMessage($player);
        }
    }

    public function onPlayerInteract(PlayerInteractEvent $event): void {
        $player = $event->getPlayer();
        if ($this->isFastStopEnabled && in_array("interact", $this->prohibitedActions) && !isset($this->allowedPlayers[$player->getName()])) {
            $event->cancel();
        }
    }

    public function onBlockPlace(BlockPlaceEvent $event): void {
    $player = $event->getPlayer();
    if ($this->isFastStopEnabled && in_array("place_block", $this->prohibitedActions) && !isset($this->allowedPlayers[$player->getName()])) {
        $event->cancel();
    }
}


    public function onBlockBreak(BlockBreakEvent $event): void {
        $player = $event->getPlayer();
        if ($this->isFastStopEnabled && in_array("break_block", $this->prohibitedActions) && !isset($this->allowedPlayers[$player->getName()])) {
            $event->cancel();
        }
    }

    public function onEntityDamage(EntityDamageEvent $event): void {
        if ($event instanceof EntityDamageByEntityEvent) {
            $damager = $event->getDamager();
            if ($damager instanceof Player) {
                $player = $damager;
                if ($this->isFastStopEnabled && in_array("damage", $this->prohibitedActions) && !isset($this->allowedPlayers[$player->getName()])) {
                    $event->cancel();
                }
            }
        }
    }

    public function onEntityDamageByEntity(EntityDamageByEntityEvent $event): void {
        $damager = $event->getDamager();
        if ($damager instanceof Player) {
            $player = $damager;
            if ($this->isFastStopEnabled && in_array("damage_by_entity", $this->prohibitedActions) && !isset($this->allowedPlayers[$player->getName()])) {
                $event->cancel();
            }
        }
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        if ($command->getName() === "faststop") {
            if (!$sender->hasPermission("faststop.command")) {
                $sender->sendMessage(TextFormat::RED . "You do not have permission to use this command.");
                return true;
            }

            if (count($args) === 0) {
                $sender->sendMessage(TextFormat::RED . "Usage: /faststop <enable|disable>");
                return true;
            }

            $subCommand = strtolower($args[0]);
            if ($subCommand === "enable") {
                $this->isFastStopEnabled = true;
                $sender->sendMessage(TextFormat::GREEN . "FastStop mode enabled.");
                $this->applyEffectsToAll();
            } elseif ($subCommand === "disable") {
                $this->isFastStopEnabled = false;
                $sender->sendMessage(TextFormat::RED . "FastStop mode disabled.");
                $this->removeEffectsFromAll();
            } else {
                $sender->sendMessage(TextFormat::RED . "Usage: /faststop <enable|disable>");
            }

            return true;
        }

        return false;
    }

    private function applyEffectsToAll(): void {
        foreach ($this->getServer()->getOnlinePlayers() as $player) {
            $this->applyEffects($player);
        }
    }
}
