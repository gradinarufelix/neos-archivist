<?php
declare(strict_types=1);

namespace PunktDe\Archivist\Eel;

/*
 * This file is part of the PunktDe.Archivist package.
 *
 * This package is open source software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Behat\Transliterator\Transliterator;
use Neos\Eel\ProtectedContextAwareInterface;

class ArchivistHelper implements ProtectedContextAwareInterface
{

    /**
     * @param string $string
     * @param int $position
     * @return string
     */
    public function buildSortingCharacter(?string $string, int $position = 1): string
    {
        $firstCharacter = mb_substr($string, 0, $position);

        // Transliterate (transform 北京 to 'Bei Jing')
        $firstCharacter = Transliterator::transliterate($firstCharacter);

        // Ensure only allowed characters are left
        $firstCharacter = preg_replace('/[^a-z]/', '#', $firstCharacter);

        if ($firstCharacter === '') {
            $firstCharacter = '#';
        }

        return strtoupper($firstCharacter);
    }

    public function allowsCallOfMethod($methodName)
    {
        return true;
    }
}
