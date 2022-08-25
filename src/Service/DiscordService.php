<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Image;
use App\Entity\User;
use App\Twig\UserImage;
use Symfony\Component\Notifier\Bridge\Discord\DiscordOptions;
use Symfony\Component\Notifier\Bridge\Discord\DiscordTransport;
use Symfony\Component\Notifier\Bridge\Discord\DiscordTransportFactory;
use Symfony\Component\Notifier\Bridge\Discord\Embeds\DiscordAuthorEmbedObject;
use Symfony\Component\Notifier\Bridge\Discord\Embeds\DiscordEmbed;
use Symfony\Component\Notifier\Bridge\Discord\Embeds\DiscordFieldEmbedObject;
use Symfony\Component\Notifier\Bridge\Discord\Embeds\DiscordFooterEmbedObject;
use Symfony\Component\Notifier\Bridge\Discord\Embeds\DiscordMediaEmbedObject;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Notifier\Transport\Dsn;
use Symfony\Config\FrameworkConfig;
use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

class DiscordService
{
    private ChatterInterface $chatter;


    public function __construct(ChatterInterface $chatter)
    {

        $this->chatter = $chatter;
    }
    public function sendToChannel()
    {
        $chatMessage = new ChatMessage('');

        //Sets which transporter to use
        $chatMessage->transport('discord');
        // Create Discord Embed
        $discordOptions = (new DiscordOptions())
            ->username('connor bot')
            ->addEmbed((new DiscordEmbed())
                ->color(2021216)
                ->title('A user posted a new topic')
                ->thumbnail((new DiscordMediaEmbedObject())
                    ->url("t"))
                ->addField((new DiscordFieldEmbedObject())
                    ->name('Track')
                    ->value('bous')
                    ->inline(true))
                ->addField((new DiscordFieldEmbedObject())
                    ->name('User')
                    ->value('username')
                    ->inline(true))
                ->footer((new DiscordFooterEmbedObject())
                    ->text('Added ...')
                    ->iconUrl('')));

        // Add the custom options to the chat message and send the message
        $chatMessage->options($discordOptions);

            $this->chatter->send($chatMessage);
    }

    public function sendError(string $error, string $ip, string $trace)
    {

        if($_ENV['DISCORD_ERROR_DSN'] == "discord://WEBHOOK_TOKEN_ID@default?webhook_id=WEBHOOK_ID")
        {
            return;
        }

    }

}
