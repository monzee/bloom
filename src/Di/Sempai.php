<?php

namespace Codeia\Di;

use Interop\Container\ContainerInterface;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Injects the members of a class through reflection and annotations.
 *
 * Iterates through every property of an object, possibly including non-publics,
 * examines the type if it is annotated with @var, tries to
 * {@see ContainerInterface::get()} the type from the container and assigns it
 * to the member. Nothing happens to non-annotated members. The container's
 * {@see ContainerInterface::has()} is called first and the member is
 * skipped if it returns false. The ContainerInterface::get() could still throw
 * though, depending on the implementation. Static members are ignored.
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 */
class Sempai {

    const NO = false;

    private $source;

    function __construct(ContainerInterface $source) {
        $this->source = $source;
    }

    /**
     * @param mixed $kohai     The instance to inject
     * @param bool $publicOnly Don't touch the protected privates.
     * @return mixed           The same object, but the members are now
     *                         populated with values from the container.
     */
    function inject($kohai, $publicOnly = true) {
        $obj = new \ReflectionObject($kohai);
        $flags = \ReflectionProperty::IS_PUBLIC;
        $flags = $publicOnly ? $flags : ($flags |
            \ReflectionProperty::IS_PROTECTED |
            \ReflectionProperty::IS_PRIVATE);
        $props = $obj->getProperties($flags);
        foreach ($props as $p) {
            /* @var $p \ReflectionProperty */
            $type = $this->getType($p);
            if ($type === null) {
                continue;
            }
            if (!$this->source->has($type)) {
                $name = $obj->getName() . '::$' . $p->getName();
                trigger_error(
                    "sempai noticed {$name} but can't resolve `{$type}`.",
                    E_USER_NOTICE
                );
                continue;
            }
            $p->setAccessible(true);
            $p->setValue($kohai, $this->source->get($type));
        }
        return $kohai;
    }

    private function getType(\ReflectionProperty $p) {
        $doc = $p->getDocComment();
        if (empty($doc)) {
            return null;
        }
        $i = 0;
        $state = 'root';
        $stateStack = [];
        while ($state !== 'eof') {
            list($token, $matched) = $this->lex($doc, $i, $state);
            $i += strlen($matched);
            switch ($token) {
                case 'annotation':
                    if (strtolower($matched) === '@var') {
                        $state = 'target';
                    }
                    break;
                case 'fqcn':
                    return $matched;
                case 'singleQuote':
                    $stateStack[] = $state;
                    $state = 'singleQuoted';
                    break;
                case 'doubleQuote':
                    $stateStack[] = $state;
                    $state = 'doubleQuoted';
                    break;
                case 'brace':
                    $stateStack[] = $state;
                    $state = 'block';
                    break;
                case 'closing':
                    $state = array_pop($stateStack);
                    break;
                case 'eof':
                    $state = 'eof';
                    break;
                default:
                    if (empty($matched)) {
                        throw new \LogicException(
                            "lex() matched an empty string. "
                            . "this should never happen."
                        );
                    }
                    break;
            }
        }
        return null;
    }

    private function lex($src, $i, $state) {
        static $states = [
            'root' => '~
                (?P<singleQuote> \' )|
                (?P<doubleQuote> " )|
                (?P<brace> { )|
                (?P<annotation> @ \w+ )|
                (?P<any> (?: [^\'"{@\\\\] | \\\\.)+ )
            ~x',
            'target' => '~
               (?P<fqcn> (?: \\\\? [[:alpha:]_] [[:alnum:]_]* )+ )
            ~x',
            'singleQuoted' => '~
                (?P<closing>  \' )|
                (?P<any> (?: [^\'\\\\] | \\\\. )+ )
            ~x',
            'doubleQuoted' => '~
                (?P<closing>  " )|
                (?P<any> (?: [^"\\\\] | \\\\. )+ )
            ~x',
            'block' => '~
                (?P<closing>  } )|
                (?P<brace>  { )|
                (?P<any> (?: [^{}\\\\] | \\\\. )+ )
            ~x',
        ];
        if (preg_match($states[$state], $src, $matches, 0, $i)) {
            foreach ($matches as $k => $v) {
                if (!is_int($k) && !empty($v)) {
                    return [$k, $v];
                }
            }
        }
        return ['eof', ''];
    }

}
