<?php

namespace Webard\NovaZadarma\Nova\Fields;

use Laravel\Nova\Fields\Badge;

class EnumBadge extends Badge
{
    protected $enum;

    public function enum($enum): self
    {
        $this->enum = $enum;

        $this->resolveEnumMap();
        $this->resolveEnumLabels();
        $this->resolveEnumIcons();

        return $this;
    }

    protected function resolveEnumMap()
    {

        $this->map = $this->enum::map();
    }

    protected function resolveEnumLabels()
    {

        $this->labels = $this->enum::labels();
    }

    protected function resolveEnumIcons()
    {

        $icons = $this->enum::icons();

        if (empty($icons)) {
            return;
        }

        $this->icons = $icons;
        $this->withIcons = true;
    }
}
