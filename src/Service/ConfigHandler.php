<?php

namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

class ConfigHandler
{
    private ?string $configPath;
    private mixed $config;
    private Filesystem $filesystem;
    public function __construct($configPath)
    {
        $this->filesystem = new Filesystem();
        $this->configPath = $configPath;
        $this->config = Yaml::parseFile($configPath);
    }
    public function getConfig(): mixed
    {
        return $this->config;
    }
    public function getDefaultImage(): ?string
    {
        return $this->config['parameters']['default']['userimage'];
    }
    public function getTopicPagination(): ?int
    {
        return $this->config['parameters']['pagination']['app.topic.pages'];
    }
    public function getCommentPagination(): ?int
    {
        return $this->config['parameters']['pagination']['app.comment.pages'];
    }
    public function setTopicPagination(int $page): mixed
    {
        $this->config['parameters']['pagination']['app.comment.pages'] = $page;
        return $this->config;
    }
    public function setCommentPagination(int $page): mixed
    {
        $this->config['parameters']['pagination']['app.post.pages'] = $page;
        return $this->config;
    }
    public function setDefaultImage(string $imageFileName): mixed
    {
        $this->config['parameters']['default']['userimage'] = $imageFileName;
        return $this->config;
    }
    public function saveConfig(): void
    {
        $this->filesystem->dumpFile($this->configPath, YAML::dump($this->config, 10));
    }
}
