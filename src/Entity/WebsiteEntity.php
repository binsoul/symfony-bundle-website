<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\Website\Entity;

use BinSoul\Common\I18n\DefaultLocale;
use BinSoul\Symfony\Bundle\I18n\Entity\CountryEntity;
use BinSoul\Symfony\Bundle\I18n\Entity\CurrencyEntity;
use BinSoul\Symfony\Bundle\I18n\Entity\LanguageEntity;
use BinSoul\Symfony\Bundle\I18n\Entity\LocaleEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents a website.
 */
#[ORM\Table(name: 'website')]
#[ORM\UniqueConstraint(columns: ['name'])]
#[ORM\Entity]
class WebsiteEntity
{
    /**
     * @var int
     */
    public const LOCALE_TYPE_PARAMETER = 1;

    /**
     * @var int
     */
    public const LOCALE_TYPE_PATH = 2;

    /**
     * @var int
     */
    public const LOCALE_TYPE_SUBDOMAIN = 3;

    /**
     * @var int|null ID of the website
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id;

    /**
     * @var string Name of the website
     */
    #[ORM\Column(type: 'string', length: 64, nullable: false)]
    private string $name;

    /**
     * @var string|null Theme of the website
     */
    #[ORM\Column(type: 'string', length: 64, nullable: true)]
    private ?string $theme = null;

    /**
     * @var string|null Logo 1 of the website
     */
    #[ORM\Column(type: 'string', length: 128, nullable: true)]
    private ?string $logo1 = null;

    /**
     * @var string|null Logo 2 of the website
     */
    #[ORM\Column(type: 'string', length: 128, nullable: true)]
    private ?string $logo2 = null;

    /**
     * @var string|null Copyright of the website
     */
    #[ORM\Column(type: 'string', length: 64, nullable: true)]
    private ?string $copyright = null;

    /**
     * @var string|null Prefix for the meta title
     */
    #[ORM\Column(type: 'string', length: 64, nullable: true)]
    private ?string $metaTitlePrefix = null;

    /**
     * @var string|null Suffix for the meta title
     */
    #[ORM\Column(type: 'string', length: 64, nullable: true)]
    private ?string $metaTitleSuffix = null;

    #[ORM\ManyToOne(targetEntity: LocaleEntity::class)]
    #[ORM\JoinColumn(nullable: false)]
    private LocaleEntity $defaultLocale;

    /**
     * @var LocaleEntity[]|Collection<int, LocaleEntity>
     */
    #[ORM\JoinTable(name: 'website_locale')]
    #[ORM\JoinColumn(name: 'website_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'locale_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\ManyToMany(targetEntity: LocaleEntity::class)]
    private Collection $additionalLocales;

    /**
     * @var int Type of the locale selection
     */
    #[ORM\Column(type: 'integer', nullable: false, options: ['default' => 1])]
    private int $localeType = self::LOCALE_TYPE_PARAMETER;

    #[ORM\ManyToOne(targetEntity: CountryEntity::class)]
    #[ORM\JoinColumn(nullable: false)]
    private CountryEntity $defaultCountry;

    /**
     * @var CountryEntity[]|Collection<int, CountryEntity>
     */
    #[ORM\JoinTable(name: 'website_country')]
    #[ORM\JoinColumn(name: 'website_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'country_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\ManyToMany(targetEntity: CountryEntity::class)]
    private Collection $additionalCountries;

    #[ORM\ManyToOne(targetEntity: CurrencyEntity::class)]
    #[ORM\JoinColumn(nullable: false)]
    private CurrencyEntity $defaultCurrency;

    /**
     * @var CurrencyEntity[]|Collection<int, CurrencyEntity>
     */
    #[ORM\JoinTable(name: 'website_currency')]
    #[ORM\JoinColumn(name: 'website_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'currency_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\ManyToMany(targetEntity: CurrencyEntity::class)]
    private Collection $additionalCurrencies;

    #[ORM\Column(type: 'boolean', nullable: false)]
    private bool $isVisible = false;

    /**
     * Constructs an instance of this class.
     */
    public function __construct(?int $id = null)
    {
        $this->id = $id;

        $this->additionalLocales = new ArrayCollection();
        $this->additionalCountries = new ArrayCollection();
        $this->additionalCurrencies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getTheme(): ?string
    {
        return $this->theme;
    }

    public function setTheme(?string $theme): void
    {
        $this->theme = $theme;
    }

    public function getLogo1(): ?string
    {
        return $this->logo1;
    }

    public function setLogo1(?string $logo1): void
    {
        $this->logo1 = $logo1;
    }

    public function getLogo2(): ?string
    {
        return $this->logo2;
    }

    public function setLogo2(?string $logo2): void
    {
        $this->logo2 = $logo2;
    }

    public function getCopyright(): ?string
    {
        return $this->copyright;
    }

    public function setCopyright(?string $copyright): void
    {
        $this->copyright = $copyright;
    }

    public function getMetaTitlePrefix(): ?string
    {
        return $this->metaTitlePrefix;
    }

    public function setMetaTitlePrefix(?string $metaTitlePrefix): void
    {
        $this->metaTitlePrefix = $metaTitlePrefix;
    }

    public function getMetaTitleSuffix(): ?string
    {
        return $this->metaTitleSuffix;
    }

    public function setMetaTitleSuffix(?string $metaTitleSuffix): void
    {
        $this->metaTitleSuffix = $metaTitleSuffix;
    }

    public function getDefaultLocale(): LocaleEntity
    {
        return $this->defaultLocale;
    }

    public function setDefaultLocale(LocaleEntity $defaultLocale): void
    {
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * @return Collection<int, LocaleEntity>|LocaleEntity[]
     */
    public function getAdditionalLocales(): Collection
    {
        return $this->additionalLocales;
    }

    /**
     * @return LocaleEntity[]
     */
    public function getAllLocales(): array
    {
        $result = $this->additionalLocales->toArray();
        array_unshift($result, $this->defaultLocale);

        return $result;
    }

    /**
     * @return LanguageEntity[]
     */
    public function getAllLanguages(): array
    {
        $result = [];

        $language = $this->defaultLocale->getLanguage();
        $result[$language->getIso2()] = $language;

        foreach ($this->additionalLocales as $additionalLocale) {
            $language = $additionalLocale->getLanguage();
            $result[$language->getIso2()] = $language;
        }

        return array_values($result);
    }

    /**
     * Chooses an available locale for the given locale code. If allowed the first locale with the same language will be returned in case no other locale matches.
     */
    public function chooseAvailableLocale(string $localeCode, bool $allowAnyLocaleWithSameLanguage = false): ?LocaleEntity
    {
        $locale = DefaultLocale::fromString($localeCode);
        $sameLanguageLocale = null;

        while (! $locale->isRoot()) {
            foreach ($this->getAllLocales() as $availableLocale) {
                if ($locale->getCode() === $availableLocale->getCode()) {
                    return $availableLocale;
                }

                if ($sameLanguageLocale === null && $locale->getLanguage() === $availableLocale->getLanguage()->getIso2()) {
                    $sameLanguageLocale = $availableLocale;
                }
            }

            $locale = $locale->getParent();
        }

        return $allowAnyLocaleWithSameLanguage ? $sameLanguageLocale : null;
    }

    public function getLocaleType(): int
    {
        return $this->localeType;
    }

    public function setLocaleType(int $localeType): void
    {
        $this->localeType = $localeType;
    }

    public function getDefaultCountry(): CountryEntity
    {
        return $this->defaultCountry;
    }

    public function setDefaultCountry(CountryEntity $defaultCountry): void
    {
        $this->defaultCountry = $defaultCountry;
    }

    /**
     * @return Collection<int, CountryEntity>|CountryEntity[]
     */
    public function getAdditionalCountries(): Collection
    {
        return $this->additionalCountries;
    }

    /**
     * @return CountryEntity[]
     */
    public function getAllCountries(): array
    {
        $result = $this->additionalCountries->toArray();
        array_unshift($result, $this->defaultCountry);

        return $result;
    }

    public function getDefaultCurrency(): CurrencyEntity
    {
        return $this->defaultCurrency;
    }

    public function setDefaultCurrency(CurrencyEntity $defaultCurrency): void
    {
        $this->defaultCurrency = $defaultCurrency;
    }

    /**
     * @return Collection<int, CurrencyEntity>|CurrencyEntity[]
     */
    public function getAdditionalCurrencies(): Collection
    {
        return $this->additionalCurrencies;
    }

    /**
     * @return CurrencyEntity[]
     */
    public function getAllCurrencies(): array
    {
        $result = $this->additionalCurrencies->toArray();
        array_unshift($result, $this->defaultCurrency);

        return $result;
    }

    public function isVisible(): bool
    {
        return $this->isVisible;
    }

    public function setIsVisible(bool $isVisible): void
    {
        $this->isVisible = $isVisible;
    }
}
