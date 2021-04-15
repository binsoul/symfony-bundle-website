<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\Website\Entity;

use BinSoul\Symfony\Bundle\I18n\Entity\LocaleEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents one domain of a website.
 *
 * @ORM\Entity()
 * @ORM\Table(
 *     name="domain",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(columns={"url"}),
 *     }
 * )
 */
class DomainEntity
{
    /**
     * @var int|null ID of the domain
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string URL of the domain
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $url;

    /**
     * @var WebsiteEntity
     * @ORM\ManyToOne(targetEntity="\BinSoul\Symfony\Bundle\Website\Entity\WebsiteEntity")
     * @ORM\JoinColumn(nullable=false)
     */
    private $website;

    /**
     * @var LocaleEntity|null
     * @ORM\ManyToOne(targetEntity="\BinSoul\Symfony\Bundle\I18n\Entity\LocaleEntity")
     * @ORM\JoinColumn(nullable=true)
     */
    private $defaultLocale;

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
