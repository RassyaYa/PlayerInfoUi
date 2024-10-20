<?php

namespace PlayerInfoUI;

use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\form\SimpleForm;
use pocketmine\utils\TextFormat;

class Main extends PluginBase {

    public function onEnable(): void {
        $this->getLogger()->info(TextFormat::GREEN . "PlayerInfoUI plugin diaktifkan!");
    }

    public function onDisable(): void {
        $this->getLogger()->info(TextFormat::RED . "PlayerInfoUI plugin dinonaktifkan!");
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        if ($command->getName() === "playerinfo") {
            if ($sender instanceof Player) {
                $this->showPlayerInfoForm($sender);
            } else {
                $sender->sendMessage(TextFormat::RED . "Perintah ini hanya dapat digunakan oleh pemain dalam game.");
            }
            return true;
        }
        return false;
    }

    private function showPlayerInfoForm(Player $player): void {
        $form = new SimpleForm(function (Player $player, ?int $data) {
            if ($data === null) {
                return; // Jika form ditutup tanpa memilih tombol
            }
            if ($data === 0) { // Jika menekan tombol Refresh
                $this->showPlayerInfoForm($player);
            }
        });

        $form->setTitle("📊 Informasi Pemain");
        $form->setContent($this->getPlayerInfo($player));
        $form->addButton("Segarkan Informasi");
        $form->addButton("Tutup");

        $player->sendForm($form);
    }

    private function getPlayerInfo(Player $player): string {
        $name = $player->getName();
        $health = $player->getHealth();
        $maxHealth = $player->getMaxHealth();
        $level = $player->getLevel()->getName();
        $x = round($player->getX(), 2);
        $y = round($player->getY(), 2);
        $z = round($player->getZ(), 2);

        // Menambahkan waktu bermain (jika ada)
        $playTime = $player->getServer()->getPlayerPlayTime($player->getName()) ?? 0; // dalam detik
        $playTimeFormatted = gmdate("H:i:s", $playTime); // Format jam:menit:detik

        return TextFormat::YELLOW . "===== Informasi Pemain =====\n" .
               TextFormat::AQUA . "Nama: " . TextFormat::WHITE . $name . "\n" .
               TextFormat::AQUA . "Kesehatan: " . TextFormat::WHITE . $health . "/" . $maxHealth . "\n" .
               TextFormat::AQUA . "Level: " . TextFormat::WHITE . $level . "\n" .
               TextFormat::AQUA . "Posisi: " . TextFormat::WHITE . "X: $x, Y: $y, Z: $z\n" .
               TextFormat::AQUA . "Waktu Bermain: " . TextFormat::WHITE . $playTimeFormatted . "\n" .
               TextFormat::YELLOW . "===========================";
    }
}
