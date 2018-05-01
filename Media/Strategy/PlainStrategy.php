<?php declare(strict_types=1);
/**
 * Shopware 5
 * Copyright (c) shopware AG
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 */

namespace Shopware\Media\Strategy;

class PlainStrategy implements StrategyInterface
{
    public function decode(string $path): string
    {
        return basename($path);
    }

    /**
     * {@inheritdoc}
     */
    public function encode(string $filename): string
    {
        $filename = ltrim($filename, '/');
        $pathInfo = pathinfo($filename);

        if (empty($pathInfo['extension'])) {
            return '';
        }

        if (preg_match('/(_[\d]+x[\d]+(@2x)?).(?:.*)$/', $filename)) {
            $filename = 'thumbnail/' . $filename;
        }

        return $filename;
    }

    /**
     * {@inheritdoc}
     */
    public function isEncoded(string $path): bool
    {
        return (bool) preg_match('/((media\/(?:archive|image|music|pdf|temp|unknown|video)(?:\/thumbnail)?).*\/((.+)\.(.+)))/', $path);
    }

    /**
     * Name of the strategy
     */
    public function getName(): string
    {
        return 'plain';
    }
}
