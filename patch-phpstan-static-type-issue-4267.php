<?php

declare(strict_types=1);

namespace Mvorisek\Atk4\HintablePhpstanPatch;

// issue: https://github.com/phpstan/phpstan/issues/4267
// TODO remove once fixed officially

$phpstanPharPath = $argv[1];
if (substr($phpstanPharPath, -5) !== '.phar' || !file_exists($phpstanPharPath)) {
    throw new \Exception('PHPStan phar not found: ' . $phpstanPharPath);
}
$phpstanPharPath2 = substr($phpstanPharPath, 0, -5);
if (!file_exists($phpstanPharPath2) || file_get_contents($phpstanPharPath) !== file_get_contents($phpstanPharPath2)) {
    $phpstanPharPath2 = null;
}

$modifyPharFunc = function (string $path, \Closure $func) use ($phpstanPharPath, $phpstanPharPath2): void {
    $dOrig = file_get_contents('phar://' . $phpstanPharPath . '/' . $path);

    $dNew = $func($dOrig);

    // echo $dNew; // debug only

    file_put_contents('phar://' . $phpstanPharPath . '/' . $path, $dNew);
    if ($phpstanPharPath2 !== null) {
        copy($phpstanPharPath, $phpstanPharPath2);
    }
};

// do not resolve static type in PHPStan too soon,
// see https://github.com/phpstan/phpstan-src/pull/414/commits/de229d97c44bbc781df32f12419b93a4ad4dcc50
$modifyPharFunc('src/Type/Generic/TemplateTypeHelper.php', function (string $d) {
    $d = preg_replace(
        '~\s+if \(\$newType instanceof (?:\\\\PHPStan\\\\Type\\\\)?StaticType\) \{\s+\$newType = \$newType->getStaticObjectType\(\);\s+\}~',
        '',
        $d
    );

    return $d;
});
$modifyPharFunc('src/Type/ObjectType.php', function (string $d) {
    $d = preg_replace(
        '~public function getIterableValueType\(\) : \\\\PHPStan\\\\Type\\\\Type\s+\{~',
        <<<'EOT'
            $0
                    return \\PHPStan\\Type\\TypeTraverser::map($this->_getIterableValueType(), function (\\PHPStan\\Type\\Type $returnType, callable $traverse): \\PHPStan\\Type\\Type {
                        if ($returnType instanceof \\PHPStan\\Type\\StaticType) {
                            return $traverse($returnType->changeBaseClass($this->getClassReflection()));
                        }
                        return $traverse($returnType);
                    });
                }
                public function _getIterableValueType() : \\PHPStan\\Type\\Type
                {
            EOT,
        $d
        );

    return $d;
});
