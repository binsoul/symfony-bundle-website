<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\Website\Entity;

use BinSoul\Symfony\Bundle\I18n\Entity\LocaleEntity;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'website_locale')]
#[ORM\UniqueConstraint(columns: ['website_id', 'locale_id'])]
#[ORM\Entity]
class WebsiteLocaleEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id;

    #[ORM\ManyToOne(targetEntity: WebsiteEntity::class, inversedBy: 'additionalLocales')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private WebsiteEntity $website;

    #[ORM\ManyToOne(targetEntity: LocaleEntity::class, fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private LocaleEntity $locale;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private bool $defaultForLanguage = false;

    /**
     * Constructs an object of this class.
     */
    public function __construct(?int $id = null)
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWebsite(): WebsiteEntity
    {
        return $this->website;
    }

    public function setWebsite(WebsiteEntity $website): void
    {
        $this->website = $website;
    }

    public function getLocale(): LocaleEntity
    {
        return $this->locale;
    }

    public function setLocale(LocaleEntity $locale): void
    {
        $this->locale = $locale;
    }

    public function isDefaultForLanguage(): bool
    {
        return $this->defaultForLanguage;
    }

    public function setDefaultForLanguage(bool $defaultForLanguage): void
    {
        $this->defaultForLanguage = $defaultForLanguage;
    }
}
