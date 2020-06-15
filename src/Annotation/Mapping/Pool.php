<?php

namespace NeoP\Pool\Annotation\Mapping;

use NeoP\Annotation\Annotation\Mapping\AnnotationMappingInterface;

use function annotationBind;

/** 
 * @Annotation 
 * @Target("CLASS")
 * @Attributes({
 *     @Attribute("type", type="string")
 * })
 *
 */
final class Pool implements AnnotationMappingInterface
{
    private $type;
    
    function __construct($params)
    {
        annotationBind($this, $params, 'setType');
    }

    public function setType(?string $type = ''): void
    {
        $this->type = $type;
    }

    public function getType(): ?string
    {
        return $this->type;
    }
}