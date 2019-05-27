<?php

/**
 * This file is part of contao-community-alliance/contao-polyfill-bundle.
 *
 * (c) 2013-2019 Contao Community Alliance.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    contao-community-alliance/contao-polyfill-bundle
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @copyright  2013-2019 Contao Community Alliance.
 * @license    https://github.com/contao-community-alliance/contao-polyfill-bundle/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace ContaoCommunityAlliance\Polyfill\Test;

use ContaoCommunityAlliance\Polyfill\CcaContaoPolyfillBundle;
use ContaoCommunityAlliance\Polyfill\DependencyInjection\Compiler\RegisterHookListenersCompiler;
use PackageVersions\Versions;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * The test for the bundle.
 *
 * @covers \ContaoCommunityAlliance\Polyfill\CcaContaoPolyfillBundle
 */
class CcaContaoPolyfillBundleTest extends TestCase
{
    public function dataProviderBuild(): array
    {
        return [
            ['4.5.0', [RegisterHookListenersCompiler::class]]
        ];
    }

    /**
     * @dataProvider dataProviderBuild
     */
    public function testBundle(string $testVersion, array $testPasses): void
    {
        try {
            $version = \ltrim(\strstr(Versions::getVersion('contao/core-bundle'), '@', true), 'v');
        } catch (\OutOfBoundsException $e) {
            $version = \ltrim(\strstr(Versions::getVersion('contao/contao'), '@', true), 'v');
        }

        $passes    = (\version_compare($version, $testVersion, '<')) ? $testPasses : [];
        $container = $this->createMock(ContainerBuilder::class);
        $container
            ->expects($this->exactly(\count($passes)))
            ->method('addCompilerPass')
            ->with(
                $this->callback(
                    function ($param) use ($passes) {
                        return \in_array(\get_class($param), $passes, true);
                    }
                )
            );

        $bundle = new CcaContaoPolyfillBundle();
        $bundle->build($container);
    }
}
