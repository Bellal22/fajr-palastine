<?php

namespace Modules\Media\Forms\Components;

use Elnooronline\LaravelBootstrapForms\Components\BaseComponent;

class ImageComponent extends BaseComponent
{
    /**
     * The component view path.
     *
     * @var string
     */
    protected $viewPath = 'media::image';

    /**
     * @var \Illuminate\Contracts\Support\Arrayable
     */
    protected $files;

    /**
     * @var int
     */
    protected $max;

    /**
     * @var string
     */
    protected $notes;

    /**
     * Initialized the input arguments.
     *
     * @param null $name
     * @param array $files
     * @return $this
     */
    public function init($name = null, $files = [])
    {
        $this->name = $name;

        $this->value = $files;

        $this->setDefaultLabel();

        $this->setDefaultNote();

        return $this;
    }

    /**
     * Set the stored files.
     *
     * @param array $files
     * @return $this
     */
    public function files($files = [])
    {
        $this->files = $files;

        return $this;
    }

    /**
     * Set the maximum files length.
     *
     * @param int $max
     * @return $this
     */
    public function max($max = 1)
    {
        $this->max = $max;

        return $this;
    }

    /**
     * @param string $notes
     * @return $this
     */
    public function notes($notes = null)
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * The variables with registered in view component.
     *
     * @return array
     */
    protected function viewComposer()
    {
        return [
            'files' => $this->files,
            'max' => $this->max,
            'notes' => $this->notes,
        ];
    }
}
