<?php
namespace Bear\Routing;

/**
 * Class RoutingResolution
 *
 * @package Bear\Routing
 */
class RoutingResolution
{
    const NOT_FOUND          = 1;
    const FOUND              = 2;

    /**
     * @var int
     */
    private $code = self::NOT_FOUND;

    /**
     * @var array
     */
    private $vars = [];

    /**
     * @var null|string
     */
    private $controller = null;

    /**
     * @var null|string
     */
    private $action = null;

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @param int $code
     *
     * @return self
     */
    public function setCode(int $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return array
     */
    public function getVars(): array
    {
        return $this->vars;
    }

    /**
     * @param array $vars
     *
     * @return self
     */
    public function setVars(array $vars): self
    {
        $this->vars = $vars;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getController(): ?string
    {
        return $this->controller;
    }

    /**
     * @param null|string $controller
     *
     * @return self
     */
    public function setController(?string $controller): self
    {
        $this->controller = $controller;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getAction(): ?string
    {
        return $this->action;
    }

    /**
     * @param null|string $action
     *
     * @return self
     */
    public function setAction(?string $action): self
    {
        $this->action = $action;

        return $this;
    }
}
