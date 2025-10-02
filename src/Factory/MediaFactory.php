<?php

namespace App\Factory;

use App\Entity\Media;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Media>
 */
final class MediaFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct() {}

    public static function class(): string
    {
        return Media::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function Defaults(): array
    {
        return [
            'title' => self::faker()->sentence(3),
            'path' => 'images/ina.webp',
            // Lien vers un Album aléatoire déjà créé
            'album' => AlbumFactory::random(),
            // Lien vers un User aléatoire si besoin
            'user' => UserFactory::random(),
        ];
    }
    public static function createMediaAlbum1($album = null): Media
    {
        return self::createOne([
            'title' => 'Image INA',
            'path' => 'images/ina.webp',
            'album' => AlbumFactory::find(['id' => "1"]),
        ]);
    }
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Media $media): void {})
        ;
    }
}
