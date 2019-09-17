<?php
/**
 * Created by PhpStorm.
 * User: Home
 * Date: 2019-03-17
 * Time: 오전 8:33
 */

namespace nlog\BanPlus\tasks;

use pocketmine\Player;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\Internet;

class VPNCheck extends AsyncTask {

    /** @var string */
    private $name, $ip;

    /** @var bool */
    private $useVPN;

    public function __construct(string $name, string $ip) {
        $this->name = $name;
        $this->ip = $ip;
        $this->useVPN = true;
    }

    public function onRun(): void {
        $url = "http://ip-api.com/json/" . $this->ip;
        $json = json_decode(Internet::getURL($url, 5, [], $err, $headers, $httpCode), true);
        if ($httpCode !== 200) {
            $this->useVPN = false;
        }
        if (is_array($json) && ($json['timezone'] ?? 'Asia/Seoul') === 'Asia/Seoul') {
            $this->useVPN = false;
        }
    }

    public function onCompletion(): void {
        $server = Server::getInstance();
        $player = $server->getPlayerExact($this->name);
        if ($this->useVPN) {
            //$server->getNameBans()->addBan($this->name, "VPN Your account has been blocked for use. \ NIf you live abroad, please contact your band post..");
            if ($player instanceof Player) {
                $player->close("", TextFormat::colorize("You have been kicked for using a VPN. Please turn off your VPN, and try again."));
            }
        }
    }

}
