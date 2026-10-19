<?php

namespace Loki\AdminComponents\Form\Field\FieldType;

interface EntitySelectInterface
{
    public function getIdField(): string;

    /**
     * @param mixed $item Selected entity, or null when nothing has been selected yet
     */
    public function getPreviewValue($item): string;
    public function getPreviewTemplate(): string;
}
