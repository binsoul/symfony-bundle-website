<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\Website\Entity;

use BinSoul\Symfony\Bundle\I18n\Entity\LocaleEntity;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents one domain of a website.
 */
#[ORM\Table(name: 'domain')]
#[ORM\UniqueConstraint(columns: ['url'])]
#[ORM\Entity]
class DomainEntity
{
    /**
     * @var int|null ID of the domain
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id;

    /**
     * @var string URL of the domain
     */
    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $url;

    #[ORM\ManyToOne(targetEntity: WebsiteEntity::class)]
    #[ORM\JoinColumn(nullable: false)]
    private WebsiteEntity $website;

    #[ORM\ManyToOne(targetEntity: LocaleEntity::class)]
    #[ORM\JoinColumn]
    private ?LocaleEntity $defaultLocale = null;

    /**
     * Constructs an instance of this class.
     */
    public function __construct(?int $id = null)
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): void
    {
        $this->url = rtrim($url, '/');
    }

    public function getWebsite(): WebsiteEntity
    {
        return $this->website;
    }

    public function setWebsite(WebsiteEntity $website): void
    {
        $this->website = $website;
    }

    public function getDefaultLocale(): ?LocaleEntity
    {
        return $this->defaultLocale;
    }

    public function setDefaultLocale(?LocaleEntity $defaultLocale): void
    {
        $this->defaultLocale = $defaultLocale;
    }
}
